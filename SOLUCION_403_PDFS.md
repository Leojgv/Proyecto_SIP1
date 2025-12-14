# Solución para Error 403 Forbidden al Descargar/Visualizar PDFs en AWS Linux

## Problema
Al intentar descargar o visualizar archivos PDF en el servidor AWS Linux, se obtiene un error **403 Forbidden**.

## Solución Implementada

Se ha implementado una solución que sirve los PDFs a través de Laravel en lugar de acceso directo al sistema de archivos. Esto evita problemas de permisos.

### Cambios Realizados:
1. ✅ Método `download()` agregado en `EvidenciaController` para servir archivos de forma segura
2. ✅ Ruta `/evidencias/{evidencia}/download` creada
3. ✅ Vistas actualizadas para usar la nueva ruta en lugar de `Storage::url()`

### Cómo Funciona Ahora:
- Los PDFs se sirven a través de Laravel usando `response()->file()`
- Se verifica la existencia del archivo en múltiples ubicaciones posibles
- Se establecen los headers HTTP correctos para visualización en el navegador

## Solución Alternativa: Arreglar Permisos en AWS Linux

Si prefieres usar acceso directo al sistema de archivos (Storage::url), sigue estos pasos:

### 1. Verificar el Enlace Simbólico

```bash
# Conectarse al servidor AWS
ssh usuario@tu-servidor-aws

# Ir al directorio del proyecto
cd /ruta/a/tu/proyecto

# Verificar si existe el enlace simbólico
ls -la public/storage

# Si no existe, crearlo
php artisan storage:link
```

### 2. Arreglar Permisos de Directorios y Archivos

```bash
# Establecer permisos para el directorio storage
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Establecer permisos para el directorio public/storage (enlace simbólico)
sudo chmod -R 755 public/storage

# Si estás usando Apache, cambiar el propietario a www-data
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache

# Si estás usando Nginx, cambiar el propietario a nginx o www-data
sudo chown -R nginx:nginx storage
sudo chown -R nginx:nginx bootstrap/cache
```

### 3. Verificar el Usuario del Servidor Web

```bash
# Para Apache
ps aux | grep apache
# Buscar el usuario (generalmente www-data o apache)

# Para Nginx
ps aux | grep nginx
# Buscar el usuario (generalmente nginx o www-data)
```

### 4. Verificar Permisos de Archivos PDF Específicos

```bash
# Ver permisos de archivos en storage/app/public
ls -la storage/app/public/

# Si los PDFs tienen permisos incorrectos (por ejemplo 600), cambiarlos a 644
sudo find storage/app/public -type f -name "*.pdf" -exec chmod 644 {} \;

# Cambiar permisos de directorios a 755
sudo find storage/app/public -type d -exec chmod 755 {} \;
```

### 5. Verificar Configuración de SELinux (si está habilitado)

```bash
# Verificar si SELinux está habilitado
getenforce

# Si está en "Enforcing", temporalmente puedes ponerlo en "Permissive" para probar
sudo setenforce 0

# O configurar el contexto correcto para el directorio storage
sudo chcon -R -t httpd_sys_rw_content_t storage/
sudo chcon -R -t httpd_sys_rw_content_t bootstrap/cache/
```

### 6. Verificar Configuración de Apache/Nginx

#### Para Apache (.htaccess):
El archivo `.htaccess` en `public/` debería permitir el acceso a archivos. Verifica que no haya reglas que bloqueen archivos PDF.

#### Para Nginx:
Asegúrate de que la configuración permita servir archivos estáticos:

```nginx
location /storage {
    alias /ruta/a/tu/proyecto/storage/app/public;
    try_files $uri $uri/ =404;
}
```

### 7. Reiniciar el Servidor Web

```bash
# Para Apache
sudo systemctl restart apache2
# o
sudo service apache2 restart

# Para Nginx
sudo systemctl restart nginx
# o
sudo service nginx restart
```

## Verificación

Después de aplicar los cambios, verifica:

```bash
# Verificar permisos
ls -la storage/app/public/
ls -la public/storage

# Probar acceso a un archivo PDF específico
curl -I http://tu-dominio.com/storage/ruta/al/archivo.pdf
```

Deberías recibir un código HTTP 200, no 403.

## Nota Importante

La solución implementada (servir archivos a través de Laravel) es **más segura** porque:
- Permite control de acceso a través de middleware
- No depende de permisos del sistema de archivos
- Funciona en cualquier entorno sin configuración adicional
- Es más fácil de mantener

Si decides usar la solución de permisos, asegúrate de mantenerlos actualizados cuando subas nuevos archivos.

