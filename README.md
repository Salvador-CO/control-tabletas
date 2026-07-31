# 📱 Control de Tabletas — Colegio de Bachilleres

Sistema web para el **control, asignación y seguimiento de dispositivos tecnológicos** (tabletas, celulares, laptops) del Colegio de Bachilleres.

---

## 🗂️ Tabla de Contenidos

- [¿Qué hace este sistema?](#-qué-hace-este-sistema)
- [Tecnologías utilizadas](#-tecnologías-utilizadas)
- [Arquitectura del proyecto](#-arquitectura-del-proyecto)
- [Instalación y configuración](#-instalación-y-configuración)
- [Manual de usuario](#-manual-de-usuario)
- [Flujo principal de trabajo](#-flujo-principal-de-trabajo)
- [Módulos del sistema](#-módulos-del-sistema)
- [Base de datos](#-base-de-datos)

---

## ✅ ¿Qué hace este sistema?

Antes del sistema, el control de tabletas se llevaba en un **archivo Excel manual**. Este sistema automatiza todo ese proceso:

| Antes (Excel) | Ahora (Sistema Web) |
|---|---|
| Anotar tabletas a mano | Inventario digital con estados en tiempo real |
| Crear vales en Word/papel | Generar PDF automático listo para imprimir |
| No saber dónde está cada tableta | Ver el estado de cada equipo al instante |
| Liberar tabletas tachando en papel | Palomita ✓ digital con fecha y hora exacta |
| Sin historial de asignaciones | Historial completo por periodo Exacer |

### Funciones principales

- 📋 **Inventario** de todos los dispositivos con número de serie, marca, modelo y estado
- 👥 **Gestión de personal** (coordinadores por sede)
- 🏫 **Gestión de sedes/planteles**
- 📅 **Registro de EXACER** (periodos de evaluación cada 3 meses)
- 📄 **Generación de Vales de Resguardo** en PDF con líneas de firma
- ✅ **Liberación rápida** con palomita verde cuando regresan las tabletas
- 📊 **Dashboard** con estadísticas en tiempo real

---

## 🛠️ Tecnologías utilizadas

| Capa | Tecnología |
|---|---|
| **Backend** | PHP 8.2 + Laravel 12 |
| **Base de datos** | SQLite (archivo local, sin servidor adicional) |
| **Frontend** | Bootstrap 5.3 + Bootstrap Icons |
| **PDF** | barryvdh/laravel-dompdf |
| **Fuentes** | Google Fonts — Inter |
| **Servidor local** | XAMPP / PHP artisan serve |

---

## 🏗️ Arquitectura del proyecto

```
control-tabletas/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php    # Estadísticas del dashboard
│   │   ├── DeviceController.php       # CRUD de dispositivos
│   │   ├── AssignmentController.php   # Vales de resguardo + liberación
│   │   ├── StaffController.php        # Gestión de personal
│   │   ├── LocationController.php     # Gestión de sedes
│   │   └── EventController.php        # Gestión de Exacers
│   └── Models/
│       ├── Category.php        # Categorías (Tableta, Celular, Laptop)
│       ├── Device.php          # Dispositivos del inventario
│       ├── Location.php        # Sedes / planteles
│       ├── Staff.php           # Personal coordinador
│       ├── Event.php           # Exacers / periodos
│       ├── Assignment.php      # Cabecera del vale de resguardo
│       └── AssignmentItem.php  # Línea por dispositivo en el vale
├── database/
│   ├── migrations/             # Estructura de todas las tablas
│   └── seeders/
│       └── DatabaseSeeder.php  # Datos de ejemplo (15 tabletas, 5 sedes...)
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php       # Layout principal con sidebar Bootstrap
│   ├── dashboard.blade.php     # Pantalla de inicio con estadísticas
│   ├── devices/index.blade.php # Inventario + formulario de alta
│   ├── assignments/
│   │   ├── index.blade.php     # Lista de todos los vales
│   │   ├── create.blade.php    # Crear nuevo vale (selección de tabletas)
│   │   └── show.blade.php      # Ver vale + liberar tabletas (palomita)
│   ├── staff/index.blade.php   # Gestión de personal
│   ├── locations/index.blade.php # Gestión de sedes
│   ├── events/index.blade.php  # Gestión de Exacers
│   └── pdf/
│       └── vale_resguardo.blade.php  # Plantilla PDF imprimible
└── routes/
    └── web.php                 # Todas las rutas del sistema
```

---

## ⚙️ Instalación y configuración

### Requisitos previos
- PHP 8.2 o superior
- Composer
- XAMPP (o cualquier servidor local con PHP)
- Git

### Pasos de instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/Salvador-CO/control-tabletas.git
cd control-tabletas

# 2. Instalar dependencias PHP
composer install

# 3. Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# 4. Crear y migrar la base de datos con datos de ejemplo
php artisan migrate:fresh --seed

# 5. Iniciar el servidor de desarrollo
php artisan serve
```

Luego abre tu navegador en: **http://127.0.0.1:8000**

> Si usas XAMPP directamente, accede en: **http://localhost/control-tabletas/public**

---

## 📖 Manual de usuario

### Lo primero que debe hacer el usuario

Cuando entras al sistema por primera vez encontrarás **datos de ejemplo** para explorar.
Para usarlo con tu información real, sigue este orden:

---

### PASO 1 — Registrar tus Sedes/Planteles

> 📍 Menú → Sedes

1. Haz clic en **"Sedes"** en el menú lateral izquierdo
2. En el formulario de la derecha escribe el nombre del plantel (ej. "Plantel Tuxtla Gutiérrez")
3. Agrega el estado o municipio si lo deseas
4. Haz clic en **"Guardar"**
5. Repite para cada sede donde prestarás tabletas

---

### PASO 2 — Registrar al Personal (Coordinadores)

> 👥 Menú → Personal

1. Haz clic en **"Personal"** en el menú lateral
2. Llena el nombre completo del coordinador (ej. "JUAN PÉREZ HERNÁNDEZ")
3. Escribe su rol/cargo (ej. "Coordinador Académico")
4. Selecciona la sede a la que pertenece (opcional)
5. Haz clic en **"Guardar"**
6. Repite para cada coordinador de cada sede

---

### PASO 3 — Registrar las Tabletas en Inventario

> 📱 Menú → Dispositivos

1. Haz clic en **"Dispositivos"** en el menú lateral
2. En el formulario de la derecha:
   - Selecciona la **categoría** (Tableta, Celular, Laptop)
   - Escribe la **marca** (ej. XIAOMI) y **modelo** (ej. Pad 6)
   - Captura el **número de serie** exacto
   - Anota el detalle del cargador (ej. "cargador punta", "sin cargador")
   - Agrega notas del estado físico si hay daños o detalles importantes
3. Haz clic en **"Guardar Dispositivo"**
4. Repite para cada tableta de tu inventario

> 💡 Tip: Puedes editar cualquier dispositivo haciendo clic en el ícono ✏️ de la tabla

---

### PASO 4 — Crear un Exacer (Periodo)

> 📅 Menú → Exacers / Periodos

1. Haz clic en **"Exacers / Periodos"** en el menú lateral
2. Escribe el nombre del evento (ej. "Exacer 2025-I")
3. Selecciona la fecha de inicio y de fin del periodo
4. Haz clic en **"Guardar"**

> Este paso es opcional para generar un vale, pero permite llevar historial por periodo.

---

### PASO 5 — Generar un Vale de Resguardo

> 📄 Menú → Nuevo Vale

1. Haz clic en **"Nuevo Vale"** en el menú lateral
2. Llena los datos del vale:
   - Selecciona el **Exacer** (opcional)
   - Selecciona la **Sede/Plantel** de destino *(obligatorio)*
   - Selecciona el **Coordinador** responsable *(obligatorio)*
   - Ajusta el nombre de quien entrega si es necesario
   - Selecciona la **fecha de entrega** y **fecha de devolución**
   - Anota cuántos **cargadores** se incluyen
   - Agrega observaciones si es necesario
3. Selecciona las tabletas en el panel derecho:
   - Haz clic en cualquier tableta para seleccionarla (se marca en verde ✓)
   - Opcionalmente asigna el nombre del personal a cada tableta
   - Puedes filtrar por número de serie o modelo con el buscador
4. Haz clic en **"Generar Vale de Resguardo"**

El sistema cambia automáticamente el estado de las tabletas a **"En Resguardo"**.

---

### PASO 6 — Imprimir el Vale (PDF)

> 📄 Vales de Resguardo → Ver → PDF

1. Ve al menú **"Vales de Resguardo"**
2. Encuentra el vale que acabas de crear
3. Haz clic en el ícono 👁️ para ver el detalle
4. Haz clic en **"Descargar PDF"** — se abre la vista de impresión
5. Haz clic en **"🖨 Imprimir / Guardar PDF"**
6. El coordinador recibe el documento y lo firma

---

### PASO 7 — Liberar Tabletas (cuando las devuelven)

> ✅ Vales de Resguardo → Ver → Palomita verde

Cuando el coordinador devuelve las tabletas:

1. Ve al menú **"Vales de Resguardo"**
2. Encuentra el vale correspondiente (busca por sede o fecha)
3. Haz clic en el ícono 👁️ para abrir el vale
4. Por cada tableta devuelta, haz clic en el **círculo ○** de la columna "Liberar"
5. El círculo se pone verde ✅ y registra la fecha y hora exacta de devolución
6. El estado de la tableta cambia automáticamente a **"Disponible"**
7. Cuando todas las tabletas están devueltas, el vale pasa a estado **"Completado"** 🟢

---

## 🔄 Flujo principal de trabajo

```
📥 Recibo la lista de sedes que necesitan tabletas
         │
         ▼
📱 Verifico cuántas tabletas DISPONIBLES tengo (Dashboard)
         │
         ▼
📄 Creo un VALE por cada sede
   (selecciono tabletas + coordinador + fechas)
         │
         ▼
🖨️ Imprimo el PDF y lo FIRMAN
         │
         ▼
✈️ Entrego las tabletas físicamente
         │
         ▼
⏳ Las tabletas están EN RESGUARDO durante el Exacer
         │
         ▼
📬 Las tabletas regresan (una a una o todas juntas)
         │
         ▼
✅ Voy marcando la PALOMITA de cada tableta devuelta
         │
         ▼
🟢 El vale queda COMPLETADO cuando todas regresan
```

---

## 🗄️ Módulos del sistema

| Módulo | URL | Descripción |
|---|---|---|
| **Dashboard** | `/` | Estadísticas: total, disponibles, en uso, pendientes |
| **Dispositivos** | `/devices` | Inventario completo + alta de nuevos equipos |
| **Personal** | `/staff` | Alta y edición de coordinadores |
| **Sedes** | `/locations` | Alta y edición de planteles |
| **Exacers** | `/events` | Registro de periodos de evaluación |
| **Vales** | `/assignments` | Lista de todos los vales de resguardo |
| **Nuevo Vale** | `/assignments/create` | Crear y asignar tabletas |
| **Ver Vale** | `/assignments/{id}` | Detalle + liberación con palomita |
| **PDF Vale** | `/assignments/{id}/pdf` | Vista imprimible del vale |

---

## 🗃️ Base de datos

### Tablas principales

| Tabla | Descripción |
|---|---|
| `categories` | Tipos de dispositivo (Tableta, Celular, Laptop) |
| `devices` | Inventario de equipos con número de serie y estado |
| `locations` | Sedes/planteles del Colegio de Bachilleres |
| `staff` | Personal coordinador por sede |
| `events` | Exacers / periodos de evaluación |
| `assignments` | Cabecera del vale de resguardo |
| `assignment_items` | Detalle: qué tableta corresponde a qué persona |

### Estados de un dispositivo

| Estado | Significado |
|---|---|
| `disponible` | Libre para asignar en cualquier momento |
| `en_resguardo` | Asignada en un vale activo |
| `asignado_fijo` | Persona fija (no entra al pool de Exacer) |
| `mantenimiento` | En reparación, no disponible |

### Estados de un vale

| Estado | Significado |
|---|---|
| `activo` | Hay tabletas pendientes de devolución |
| `completado` | Todas las tabletas fueron devueltas ✅ |
| `cancelado` | Vale cancelado manualmente |

---

## 🚀 Comandos útiles

```bash
# Iniciar el servidor de desarrollo
php artisan serve

# Recargar la base de datos con datos de ejemplo
php artisan migrate:fresh --seed

# Ver todas las rutas registradas
php artisan route:list

# Limpiar caché
php artisan config:clear && php artisan cache:clear
```

---

## 📈 Próximas mejoras sugeridas

- [ ] Autenticación (login/logout con usuario y contraseña)
- [ ] Reporte Excel de dispositivos por periodo
- [ ] Historial completo de movimientos por dispositivo
- [ ] Notificaciones de vencimiento (tabletas no devueltas)
- [ ] Módulo de mantenimiento (registro de reparaciones)
- [ ] Soporte para múltiples organizaciones

---

## 👤 Autor

**Salvador** — Colegio de Bachilleres DASE 2026

---

*Sistema desarrollado con Laravel 12 + Bootstrap 5 — 2026*
