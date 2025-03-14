# Cuidapp MVP
## Flujo
- User: Solicita vigilador en /zonas/contract.
- Admin: Asigna con fechas en /admin/contratos.
- Vigilador: Acepta/Rechaza en /dashboard.
- Sistema: Expira contratos con `php artisan contracts:complete-expired`.

## Instalación
1. `composer install`
2. `php artisan migrate`
3. `php artisan serve`

## Comandos
- Expirar contratos: `php artisan contracts:complete-expired`
