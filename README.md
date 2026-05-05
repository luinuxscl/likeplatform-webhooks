# LikePlatform Webhooks

Package de webhooks salientes y entrantes para LikePlatform. Permite a los usuarios configurar endpoints que reciben notificaciones de eventos de la plataforma.

## Instalación

```bash
composer require likeplatform/webhooks
```

## Características (Fase 1)

- Despacho de webhooks salientes a URLs suscritas
- Recepción y validación de webhooks entrantes con HMAC
- Gestión de endpoints y suscripciones a eventos
- Panel de historial de entregas y reintentos

## Contratos Implementados

| Contrato | Propósito |
|----------|-----------|
| `WebhookDispatcherContract` | Despacho de eventos a URLs suscritas |
| `WebhookReceiverContract` | Recepción y validación de webhooks entrantes |
| `WebhookEventContract` | Definición de eventos webhook suscribibles |

## Arquitectura

Ver [ARCHITECTURE.md](./ARCHITECTURE.md) para detalles de diseño.

## Versión

**0.1.0-dev** — Sprint 0: Estructura inicial del package

---

*Like Innovación — Powered by LikePlatform*
