# COLCAMPOS v2.0 — Sistema de Gestión Avícola

## Correcciones aplicadas en esta versión

### Errores críticos de BD corregidos
1. `guardar_alimentacion.php` — usaba `$conn` (inexistente); corregido a `mysqli_query($conexion, ...)`
2. `guardar_almacen.php` — **archivo nuevo** (faltaba completamente)
3. `guardar_pagos.php` — **archivo nuevo** (faltaba completamente)
4. `guardar_produccion.php` — nombres de campos `$_POST` sincronizados con el formulario (`h_c[]`, `h_b[]`, etc.) y tabla corregida a `registros_produccion`
5. `limpiar_mes.php` — usaba `$conn`; corregido a `$conexion`

### Mejoras visuales
- Letras blancas sobre fondo blanco en login: **corregido** (fondo negro al hacer focus)
- Botón "🏠 Inicio / Volver al Dashboard" añadido en todas las páginas internas
- Sub-navegación entre secciones del mismo galpón (Producción ↔ Alimentación ↔ Pagos ↔ Almacén)
- Cálculo automático de % Postura en la planilla de producción
- Sidebar de `seleccionar_galpon.php` con cierre al clic fuera del menú
- Incremento de filas: Almacén de 10 → 12 filas, Alimentación de 10 → 15 filas

---

## Instalación

### 1. Base de datos
Importa el archivo `sistema_avicola.sql` en phpMyAdmin:
- Abre phpMyAdmin → Importar → selecciona `sistema_avicola.sql` → Continuar

### 2. Archivos
Copia la carpeta `COLCAMPOS/` a tu carpeta de servidor:
- XAMPP: `C:/xampp/htdocs/COLCAMPOS/`
- WAMP:  `C:/wamp64/www/COLCAMPOS/`
- Linux: `/var/www/html/COLCAMPOS/`

### 3. Acceso
Abre en el navegador: `http://localhost/COLCAMPOS/`

**Credenciales por defecto:**
- Usuario: `admin`
- Contraseña: `1234`

---

## Estructura de archivos

```
COLCAMPOS/
├── index.php                  Login
├── dashboard.php              Panel principal (elección de granja)
├── seleccionar_galpon.php     Vista de galpones + barra lateral
├── gestion_granja.php         Formulario de producción mensual
├── guardar_produccion.php     Guarda producción en BD
├── alimentacion.php           Formulario de alimentación
├── guardar_alimentacion.php   Guarda alimentación en BD
├── gestion_almacen.php        Formulario de inventario
├── guardar_almacen.php        Guarda inventario en BD  ← NUEVO
├── registro_pagos.php         Formulario de nómina
├── guardar_pagos.php          Guarda pagos en BD       ← NUEVO
├── reportes_mensuales.php     Pantalla de reportes / cierre de mes
├── limpiar_mes.php            Reinicia tablas mensuales
├── logout.php                 Cierra sesión
├── conexion.php               Configuración de BD
├── sistema_avicola.sql        Script de creación de BD ← NUEVO
├── css/style.css
├── js/main.js
├── img.jpeg
└── img_fondo.jpeg
```

---

## Granjas y galpones

| Granja      | Parámetro URL  | Galpones |
|-------------|----------------|----------|
| La Ponderosa | `ponderosa`   | 1 al 6   |
| La Lupe      | `lupe`        | 1 al 3   |

Cada galpón de cada granja guarda su información **de forma independiente** en la BD gracias a los campos `id_granja` y `num_galpon`.
