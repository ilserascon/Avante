# Avante

| Requisito     | Versión Mínima   | 
|---------------|------------------|
| PHP           | 8.1 o superior   | 
| MySQL         | 5.7 o superior   | 
| XAMPP         | 8.1.x o superior | 
| Composer      | 2.x              | 
| Node.js       | 16.x o superior  | 
| npm           | 8.x o superior   | 

Usuario por default admin@avante.com contraseña: admin

## Base de datos y migraciones

### Uso diario (conserva datos)

```bash
php artisan migrate
```

Aplica solo migraciones pendientes. **Úsalo siempre** después de `git pull` o al agregar migraciones nuevas.

### Reinicio completo en desarrollo (borra todo)

Solo con `APP_ENV=local` en tu `.env`:

```bash
php artisan db:reset-dev
```

Equivale a `migrate:fresh --seed`: elimina todas las tablas, vuelve a correr migraciones y seeders. Vuelve a crear el usuario admin vía migración de roles.

Sin preguntar confirmación:

```bash
php artisan db:reset-dev --force
```

Atajo con Composer:

```bash
composer db:reset-dev
```

### Comandos que conviene evitar en este proyecto

| Comando | Riesgo |
|---------|--------|
| `migrate:refresh` | Revierte migraciones una a una; puede fallar con ENUM (`cancelada`, `completada`) o claves foráneas, y dejar la BD a medias. |
| `migrate:fresh` en producción | Borra todos los datos. |

Si solo necesitas probar migraciones nuevas, usa **`migrate`**, no `refresh`.

### Si `migrate:refresh` falló a medias

1. No vuelvas a ejecutar `refresh` a ciegas.
2. Revisa el error (a menudo estatus `cancelada` / `completada` en cotizaciones).
3. Ejecuta `php artisan migrate` para aplicar lo pendiente, o en local usa `php artisan db:reset-dev`.

