# Nexora — Mobile Collaboration Guide
> How to onboard your teammate on the Flutter part and work together efficiently.

---

## STEP 1 — Git Branching Strategy

### On YOUR side (you do this once)

```bash
# Make sure your main branches exist
git checkout main
git pull origin main

# Create the develop branch if it doesn't exist yet
git checkout -b develop
git push -u origin develop

# Protect both branches on GitHub:
# GitHub → Settings → Branches → Add rule
# Branch name: main  → require PR + 1 reviewer
# Branch name: develop → require PR + 1 reviewer
```

### What she does when she starts

```bash
# 1. Clone the repo
git clone https://github.com/YOUR_ORG/nexora-mobile.git
cd nexora-mobile

# 2. Always branch off develop — never work directly on develop or main
git checkout develop
git pull origin develop
git checkout -b feat/flutter-auth

# 3. When done with a feature, push and open a PR into develop
git add .
git commit -m "feat: implement login screen with Sanctum auth"
git push -u origin feat/flutter-auth
# → Open PR on GitHub: feat/flutter-auth → develop
```

### Branch naming conventions (share this with her)

```
feat/flutter-auth          → new features
fix/flutter-chat-scroll    → bug fixes
refactor/flutter-services  → refactoring
chore/flutter-deps         → dependencies update
```

---

## STEP 2 — Flutter Environment Setup (she runs this)

### Prerequisites

```bash
# Install Flutter (if not installed)
# On macOS:
brew install --cask flutter

# On Windows (use PowerShell as admin):
# Download Flutter SDK from https://docs.flutter.dev/get-started/install

# Verify installation
flutter doctor

# All checkmarks should be green except optional ones
```

### Clone and setup the mobile project

```bash
git clone https://github.com/YOUR_ORG/nexora-mobile.git
cd nexora-mobile

# Install dependencies
flutter pub get

# Copy environment config
cp .env.example .env
# → Edit .env and set API_BASE_URL to the staging server URL
```

### Recommended `pubspec.yaml` packages (share this with her)

```yaml
dependencies:
  flutter:
    sdk: flutter

  # HTTP & API
  dio: ^5.4.0
  retrofit: ^4.1.0

  # State management
  flutter_riverpod: ^2.5.0

  # Auth storage
  flutter_secure_storage: ^9.0.0

  # WebSocket (Reverb)
  laravel_echo: ^1.1.0
  pusher_channels_flutter: ^2.0.1

  # Navigation
  go_router: ^13.0.0

  # Audio recording
  record: ^5.1.0
  audioplayers: ^6.0.0

  # File upload
  dio: ^5.4.0
  http_parser: ^4.0.2

  # Push notifications
  firebase_core: ^2.27.0
  firebase_messaging: ^14.7.19

  # UI
  cached_network_image: ^3.3.1
  flutter_svg: ^2.0.10+1
```

### Environment config file (create `lib/config/env.dart`)

```dart
class Env {
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000/api/v1', // Android emulator localhost
  );

  static const String reverbHost = String.fromEnvironment(
    'REVERB_HOST',
    defaultValue: '10.0.2.2',
  );

  static const int reverbPort = int.fromEnvironment(
    'REVERB_PORT',
    defaultValue: 8080,
  );

  static const String reverbKey = String.fromEnvironment(
    'REVERB_KEY',
    defaultValue: 'nexora-key',
  );
}
```

### Run the app

```bash
# List available devices
flutter devices

# Run on Android emulator
flutter run --dart-define=API_BASE_URL=https://staging.nexora.ma/api/v1

# Run on iOS simulator
flutter run -d "iPhone 15" --dart-define=API_BASE_URL=https://staging.nexora.ma/api/v1

# Run in debug mode with hot reload
flutter run
# Press 'r' for hot reload, 'R' for full restart
```

---

## STEP 3 — API Documentation (Swagger)

This is the most important handoff. She builds against the docs, not your code.

### You install L5-Swagger on the Laravel side

```bash
composer require darkaonline/l5-swagger

php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### Add Swagger annotations to your controllers (example)

```php
// app/Http/Controllers/Api/V1/AuthController.php

/**
 * @OA\Post(
 *     path="/api/v1/auth/login",
 *     summary="Login user",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="user@nexora.ma"),
 *             @OA\Property(property="password", type="string", example="secret123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string"),
 *             @OA\Property(property="token_type", type="string", example="Bearer"),
 *             @OA\Property(property="expires_in", type="integer", example=900)
 *         )
 *     )
 * )
 */
public function login(LoginRequest $request): JsonResponse { ... }
```

### Generate and access docs

```bash
# Generate the swagger JSON
php artisan l5-swagger:generate

# Docs accessible at:
# http://localhost:8000/api/documentation
# https://staging.nexora.ma/api/documentation
```

She opens `https://staging.nexora.ma/api/documentation` in her browser and can see all endpoints, request bodies, and response shapes. She can even test them directly from there.

---

## STEP 4 — Shared Staging Environment (Docker + GitHub Actions)

### Your `docker-compose.yml` already has everything. For staging, she needs the URL.

```bash
# On your staging server (Render, Railway, or a VPS):
# Set these environment variables (share with her):
APP_URL=https://staging.nexora.ma
FRONTEND_URL=https://staging.nexora.ma
REVERB_HOST=staging.nexora.ma
REVERB_PORT=443
```

### GitHub Actions — auto-deploy `develop` to staging

Create `.github/workflows/staging.yml`:

```yaml
name: Deploy to Staging

on:
  push:
    branches: [develop]

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'

      - name: Install dependencies
        run: composer install --no-dev --optimize-autoloader

      - name: Run migrations
        env:
          DB_CONNECTION: pgsql
          DB_HOST: ${{ secrets.STAGING_DB_HOST }}
          DB_DATABASE: ${{ secrets.STAGING_DB_NAME }}
          DB_USERNAME: ${{ secrets.STAGING_DB_USER }}
          DB_PASSWORD: ${{ secrets.STAGING_DB_PASSWORD }}
        run: php artisan migrate --force

      - name: Deploy to server
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.STAGING_HOST }}
          username: ${{ secrets.STAGING_USER }}
          key: ${{ secrets.STAGING_SSH_KEY }}
          script: |
            cd /var/www/nexora
            git pull origin develop
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            sudo systemctl restart php8.3-fpm
```

Add secrets in GitHub → Settings → Secrets and variables → Actions.

---

## STEP 5 — Flutter API Service Layer (structure to share with her)

### Folder structure she should follow

```
lib/
├── config/
│   └── env.dart
├── core/
│   ├── api/
│   │   ├── api_client.dart        ← Dio instance + interceptors
│   │   └── api_endpoints.dart     ← All endpoint constants
│   ├── errors/
│   │   └── app_exception.dart
│   └── storage/
│       └── secure_storage.dart    ← Token storage
├── features/
│   ├── auth/
│   │   ├── data/
│   │   │   ├── auth_repository.dart
│   │   │   └── models/
│   │   ├── domain/
│   │   └── presentation/
│   │       ├── screens/
│   │       └── widgets/
│   ├── chat/
│   ├── experts/
│   └── profile/
└── main.dart
```

### `lib/core/api/api_client.dart`

```dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../config/env.dart';

class ApiClient {
  static final Dio _dio = _createDio();

  static Dio _createDio() {
    final dio = Dio(BaseOptions(
      baseUrl: Env.apiBaseUrl,
      connectTimeout: const Duration(seconds: 10),
      receiveTimeout: const Duration(seconds: 30),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));

    dio.interceptors.add(_AuthInterceptor());
    return dio;
  }

  static Dio get instance => _dio;
}

class _AuthInterceptor extends Interceptor {
  final _storage = const FlutterSecureStorage();

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.read(key: 'access_token');
    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
    }
    handler.next(options);
  }

  @override
  Future<void> onError(
    DioException err,
    ErrorInterceptorHandler handler,
  ) async {
    if (err.response?.statusCode == 401) {
      // Token expired → call refresh endpoint
      // then retry original request
    }
    handler.next(err);
  }
}
```

### `lib/core/api/api_endpoints.dart`

```dart
class ApiEndpoints {
  // Auth
  static const String register       = '/auth/register';
  static const String login          = '/auth/login';
  static const String logout         = '/auth/logout';
  static const String me             = '/auth/me';
  static const String refresh        = '/auth/refresh';
  static const String verifyEmail    = '/auth/verify-email';
  static const String forgotPassword = '/auth/forgot-password';
  static const String resetPassword  = '/auth/reset-password';

  // Users
  static const String profile        = '/users/profile';
  static const String uploadAvatar   = '/users/avatar';
  static const String notifications  = '/users/notifications';

  // Experts
  static const String experts        = '/experts';
  static String expertById(int id)   => '/experts/$id';
  static String expertReviews(int id)=> '/experts/$id/reviews';

  // Conversations
  static const String conversations  = '/conversations';
  static String conversationById(int id)      => '/conversations/$id';
  static String conversationMessages(int id)  => '/conversations/$id/messages';
  static String conversationAudio(int id)     => '/conversations/$id/messages/audio';
  static String escalate(int id)              => '/conversations/$id/escalate';
  static String closeConversation(int id)     => '/conversations/$id/close';
  static String rateConversation(int id)      => '/conversations/$id/rate';

  // Categories
  static const String categories     = '/categories';

  // Payments
  static const String stripeIntent   = '/payments/stripe/intent';
  static const String stripeConfirm  = '/payments/stripe/confirm';
}
```

---

## STEP 6 — WebSocket (Laravel Reverb) in Flutter

### Install dependencies

```bash
flutter pub add laravel_echo pusher_channels_flutter
```

### `lib/core/websocket/echo_service.dart`

```dart
import 'package:laravel_echo/laravel_echo.dart';
import 'package:pusher_channels_flutter/pusher_channels_flutter.dart';
import '../config/env.dart';
import '../storage/secure_storage.dart';

class EchoService {
  static Echo? _echo;

  static Future<void> init(String accessToken) async {
    _echo = Echo(
      broadcaster: 'reverb',
      client: PusherChannelsFlutter.getInstance(),
      options: {
        'cluster': 'mt1',
        'wsHost': Env.reverbHost,
        'wsPort': Env.reverbPort,
        'forceTLS': false,
        'enabledTransports': ['ws', 'wss'],
        'authEndpoint': '${Env.apiBaseUrl}/broadcasting/auth',
        'auth': {
          'headers': {
            'Authorization': 'Bearer $accessToken',
          },
        },
      },
    );
  }

  // Listen to messages in a conversation
  static void listenToConversation(
    int conversationId,
    Function(dynamic) onMessage,
  ) {
    _echo
        ?.private('conversation.$conversationId')
        .listen('MessageSent', onMessage);
  }

  // Listen to AI response ready
  static void listenToAIResponse(
    int conversationId,
    Function(dynamic) onResponse,
  ) {
    _echo
        ?.private('conversation.$conversationId')
        .listen('AIResponseReady', onResponse);
  }

  // Listen to user notifications
  static void listenToUserNotifications(
    int userId,
    Function(dynamic) onNotification,
  ) {
    _echo
        ?.private('user.$userId')
        .listen('NotificationReceived', onNotification);
  }

  static void disconnect() {
    _echo?.disconnect();
  }
}
```

---

## STEP 7 — Daily Workflow (both of you)

```bash
# Every morning — sync with develop
git checkout develop
git pull origin develop

# Start new task
git checkout -b feat/flutter-expert-list

# Work... commit often
git add lib/features/experts/
git commit -m "feat: add expert listing screen with filters"

# Before opening PR — rebase on develop to get latest changes
git fetch origin
git rebase origin/develop

# Push and open PR
git push -u origin feat/flutter-expert-list
# → GitHub: open PR → develop, assign you as reviewer
```

---

## STEP 8 — Quick Reference — Who Does What

| Task | You (Backend) | Her (Flutter) |
|------|--------------|---------------|
| Auth API | Build + document | Integrate in `AuthRepository` |
| WebSocket events | Broadcast via Reverb | Listen with `EchoService` |
| S3 presigned URL | Generate endpoint | Use URL to upload audio directly |
| n8n AI callback | Receive + broadcast | Listen for `AIResponseReady` event |
| Push notifications | Send via FCM job | Register device token on login |
| Swagger docs | Keep up to date | Read before building each feature |

---

## Summary of URLs to share with her

```
Staging API:       https://staging.nexora.ma/api/v1
Swagger docs:      https://staging.nexora.ma/api/documentation
Reverb WebSocket:  wss://staging.nexora.ma:443
GitHub repo:       https://github.com/YOUR_ORG/nexora-mobile
```
