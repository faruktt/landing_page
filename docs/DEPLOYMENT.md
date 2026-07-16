# Deployment & Production Maintenance

### Checklist
- Ensure storage and cache folders have write permissions (`chmod -R 775 storage bootstrap/cache`).
- Run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
- Configure Supervisor to keep queue workers running for courier sync jobs.
