# Security Guidelines

- CSRF token validation enforced on all checkout endpoints.
- Rate limiting configured to prevent automated bot order spamming.
- Strict input sanitization for customer names, phone numbers, and addresses.
- Role-based access control protecting admin dashboard routes.
