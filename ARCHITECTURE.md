# Arquitectura — likeplatform/webhooks

## Propósito

Package de webhooks para LikePlatform que implementa despacho de eventos salientes y recepción de webhooks entrantes de servicios externos.

## Estructura

```
likeplatform-webhooks/
├── src/
│   ├── Providers/WebhooksServiceProvider.php
│   ├── Http/Controllers/     ← Controllers para gestión de webhooks
│   └── Models/               ← Modelos: WebhookEndpoint, WebhookDelivery
├── routes/webhooks.php
├── database/migrations/      ← Migraciones para tablas de webhooks
├── config/webhooks.php
├── lang/en/ y lang/es/
└── tests/
```

## Contratos Implementados

### WebhookDispatcherContract

Implementa el despacho de eventos del core a URLs suscritas:

- `dispatch(string $event, array $payload): void`
- `getSubscribedUrls(string $event): array`

### WebhookReceiverContract

Implementa la recepción y validación de webhooks de servicios externos:

- `validateSignature(Request $request): bool`
- `process(string $source, array $payload): void`

### WebhookEventContract

Define los eventos que users pueden suscribir para recibir webhooks:

- `key(): string`
- `name(): string`
- `description(): string`
- `payloadSchema(): array`

## Modelos (Fase 1)

- **WebhookEndpoint**: URL + secreto + eventos suscritos
- **WebhookDelivery**: Registro de cada intento de entrega con estado y response

## Registro en el Core

```php
// En el ServiceProvider, Fase 1:
$this->app->make(WebhookEventRegistry::class)->register(
    new UserCreatedEvent()
);
```

## Dependencias

- `likeplatform/contracts ^0.1.0`
- `orchestra/testbench ^10.0` (dev)

## Estado Actual

**Sprint 0** — Estructura del package y ServiceProvider creados. Sin lógica de negocio.

---

*Like Innovación — Powered by LikePlatform*
