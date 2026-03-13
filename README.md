# 🏠 Ejercicio Git - Hogares ISN

Sistema CRUD de empleados para practicar el flujo de trabajo con Git y GitHub.

---

## 📋 Requisitos

- PHP 8.0 o superior
- MySQL 5.7 o superior
- Servidor web (Apache / Nginx) o PHP built-in server

---

## ⚡ Instalación local (cada desarrollador)

### 1. Clonar el repositorio
```bash
git clone git@github.com:TU-ORGANIZACION/ejercicio-git.git
cd ejercicio-git
```

### 2. Configurar la base de datos
```bash
# Crear la base de datos y tabla con datos de ejemplo
mysql -u root -p < sql/setup.sql
```

### 3. Configurar la conexión
```bash
# Copiar el archivo de ejemplo
cp config.example.php config.php

# Editar con tus datos locales (NUNCA subir config.php al repo)
nano config.php
```

### 4. Levantar el servidor de desarrollo
```bash
php -S localhost:8000
```

Abrir en el navegador: **http://localhost:8000**

---

## 🌿 Flujo de trabajo con Git (MUY IMPORTANTE)

### Reglas del equipo:
- ❌ **NUNCA** hacer push directo a `main`
- ✅ Siempre trabajar en una rama propia
- ✅ Abrir un Pull Request para revisión
- ✅ Esperar aprobación del administrador antes del merge

---

### Paso a paso para cada tarea:

#### 1. Antes de empezar — actualizar tu rama local
```bash
git checkout main
git pull origin main
```

#### 2. Crear tu rama de trabajo
```bash
# Nomenclatura: feature/descripcion-corta
git checkout -b feature/mi-nueva-funcionalidad
```

#### 3. Hacer tus cambios y guardarlos
```bash
# Ver qué archivos cambiaste
git status

# Agregar los cambios
git add .

# Hacer commit con mensaje descriptivo
git commit -m "feat: descripción de lo que hice"
```

#### 4. Subir tu rama a GitHub
```bash
git push origin feature/mi-nueva-funcionalidad
```

#### 5. Abrir un Pull Request
- Ir a GitHub → tu repositorio
- Clic en **"Compare & pull request"**
- Describir qué cambios hiciste y por qué
- Asignar al administrador como revisor

#### 6. Esperar revisión
- El admin revisará y aprobará o pedirá cambios
- Una vez aprobado, el admin hace el merge a `main`

---

## 📝 Convención de commits

Usamos prefijos para identificar el tipo de cambio:

| Prefijo | Uso |
|---------|-----|
| `feat:` | Nueva funcionalidad |
| `fix:` | Corrección de bug |
| `style:` | Cambios de estilos CSS |
| `refactor:` | Mejora de código sin cambiar funcionalidad |
| `docs:` | Cambios en documentación |

**Ejemplos:**
```
feat: agregar campo de teléfono en formulario
fix: corregir validación de email duplicado
style: mejorar estilos del botón eliminar
docs: actualizar instrucciones del README
```

---

## 🎯 Ejercicios para el equipo

Una vez instalado el proyecto, practica con estas tareas:

1. **Ejercicio 1** — Agrega un campo `telefono` a la tabla y al formulario
2. **Ejercicio 2** — Agrega un filtro por rango de salario en el listado
3. **Ejercicio 3** — Crea una página de "ver detalle" del empleado
4. **Ejercicio 4** — Agrega paginación al listado (10 registros por página)

Cada ejercicio debe hacerse en una rama separada y con su Pull Request.

---

## 📁 Estructura del proyecto

```
ejercicio-git/
├── index.php           # Listado de empleados
├── crear.php           # Formulario para agregar
├── editar.php          # Formulario para editar
├── eliminar.php        # Lógica de eliminación
├── config.php          # 🔒 Configuración DB (NO en git)
├── config.example.php  # Plantilla de configuración
├── css/
│   └── style.css       # Estilos
├── sql/
│   └── setup.sql       # Script de base de datos
├── .gitignore          # Archivos ignorados por git
└── README.md           # Este archivo
```

---

## 🆘 Problemas comunes

**Error de conexión a la base de datos**
→ Verifica que copiaste `config.example.php` a `config.php` y pusiste tus credenciales correctas.

**No puedo hacer push a main**
→ Correcto, está protegido. Crea una rama y abre un Pull Request.

**Olvidé hacer pull antes de crear mi rama**
→ `git checkout main && git pull && git checkout -b feature/mi-rama`
