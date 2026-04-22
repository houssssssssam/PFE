<?php

namespace App\Services;

use App\Enums\ConversationStatus;
use App\Enums\MessageSenderType;
use App\Enums\MessageType;
use App\Events\AIResponseReady;
use App\Models\AiLog;
use App\Models\Conversation;
use App\Models\Message;

class AiService
{
    public function __construct(
        private N8nService $n8nService,
        private ConversationService $conversationService,
    ) {}

    /**
     * Process a user message through the AI pipeline:
     * 1. Moderate content
     * 2. Analyze & generate response
     * 3. Auto-escalate if needed
     * 4. Log everything
     */
    public function processMessage(Message $message): void
    {
        $conversation = $message->conversation;

        // Skip if conversation is already with an expert or closed
        if (in_array($conversation->status, [ConversationStatus::Expert, ConversationStatus::Closed])) {
            return;
        }

        // Step 1: Content moderation
        $moderationResult = $this->moderate($message);

        if ($moderationResult['flagged']) {
            $this->handleFlaggedContent($message, $moderationResult);
            return;
        }

        // Step 2: Analyze & generate AI response
        $analysisResult = $this->analyze($message, $conversation);

        // Step 3: Create AI response message
        $aiMessage = $this->createAiResponse($conversation, $analysisResult['response']);

        // Step 4: Log the interaction
        $this->logInteraction($conversation, $message, $analysisResult);

        // Step 5: Broadcast AI response
        event(new AIResponseReady($aiMessage));

        // Step 6: Auto-escalate if confidence is low
        if ($analysisResult['escalate'] ?? false) {
            $this->conversationService->escalate($conversation);
        }
    }

    /**
     * Moderate a message for inappropriate content.
     */
    public function moderate(Message $message): array
    {
        return $this->n8nService->moderate([
            'message_id'      => $message->id,
            'conversation_id' => $message->conversation_id,
            'content'         => $message->content,
            'sender_type'     => $message->sender_type->value,
        ]);
    }

    /**
     * Analyze a message and generate an AI response.
     */
    public function analyze(Message $message, Conversation $conversation): array
    {
        // Build conversation history for context
        $history = $conversation->messages()
            ->orderBy('created_at')
            ->take(20)
            ->get()
            ->map(fn (Message $msg) => [
                'role'    => $msg->sender_type === MessageSenderType::User ? 'user' : 'assistant',
                'content' => $msg->content ?? '[audio]',
            ])
            ->toArray();

        return $this->n8nService->analyze([
            'message_id'      => $message->id,
            'conversation_id' => $conversation->id,
            'category_id'     => $conversation->category_id,
            'content'         => $message->content,
            'history'         => $history,
        ]);
    }

    /**
     * Summarize a conversation (called on close).
     */
    public function summarizeConversation(Conversation $conversation): array
    {
        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn (Message $msg) => [
                'role'    => $msg->sender_type->value,
                'content' => $msg->content ?? '[audio]',
                'time'    => $msg->created_at->toISOString(),
            ])
            ->toArray();

        $result = $this->n8nService->summarize([
            'conversation_id' => $conversation->id,
            'category_id'     => $conversation->category_id,
            'messages'        => $messages,
        ]);

        // Update conversation with summary
        if (! empty($result['summary'])) {
            $conversation->update(['summary' => $result['summary']]);
        }

        // Log the summarization
        AiLog::create([
            'conversation_id' => $conversation->id,
            'workflow'        => 'summarize',
            'prompt'          => json_encode(['message_count' => count($messages)]),
            'response'        => $result['summary'] ?? '',
            'model'           => $result['model'] ?? 'unknown',
            'tokens_used'     => $result['tokens_used'] ?? 0,
        ]);

        return $result;
    }

    /**
     * Create an AI response message in the conversation.
     */
    private function createAiResponse(Conversation $conversation, string $content): Message
    {
        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => MessageSenderType::Ai,
            'sender_id'       => null,
            'type'            => MessageType::Text,
            'content'         => $content,
        ]);
    }

    /**
     * Handle flagged content — create a warning message.
     */
    private function handleFlaggedContent(Message $message, array $moderationResult): void
    {
        AiLog::create([
            'conversation_id' => $message->conversation_id,
            'message_id'      => $message->id,
            'workflow'        => 'moderate',
            'prompt'          => $message->content,
            'response'        => json_encode($moderationResult),
            'model'           => 'moderation',
            'confidence'      => 1.0,
            'escalated'       => false,
        ]);

        // Optionally: soft-delete or flag the message
        // $message->update(['metadata' => ['flagged' => true, 'reason' => $moderationResult['reason']]]);
    }

    /**
     * Log an AI interaction for auditing and analytics.
     */
    private function logInteraction(Conversation $conversation, Message $message, array $result): void
    {
        AiLog::create([
            'conversation_id' => $conversation->id,
            'message_id'      => $message->id,
            'workflow'        => 'analyze',
            'prompt'          => $message->content,
            'response'        => $result['response'] ?? '',
            'model'           => $result['model'] ?? 'unknown',
            'confidence'      => $result['confidence'] ?? 0,
            'tokens_used'     => $result['tokens_used'] ?? 0,
            'escalated'       => $result['escalate'] ?? false,
        ]);
    }
}
