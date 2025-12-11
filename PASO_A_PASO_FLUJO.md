# Paso a Paso - Flujo Completo del Sistema SIP

Este documento describe exactamente cómo cada rol interactúa con el sistema y cómo se ven afectados en cada etapa del proceso.

## 🎯 Resumen del Flujo

**Estudiante** → **Coordinadora (EI)** → **Asesora Técnica (CTP)** → **Director de Carrera (DC)** → **Docentes**

---

## 📋 Paso 1: Estudiante solicita entrevista

### ¿Qué hace el Estudiante?
1. Ingresa al sistema como **Estudiante**
2. Va a "Solicitar Entrevista"
3. Completa el formulario:
   - Título de la solicitud
   - Descripción de su necesidad
   - Selecciona un cupo disponible de la Coordinadora
   - Acepta autorización
4. Envía la solicitud

### ¿Qué sucede en el sistema?
- ✅ Se crea una `Solicitud` con estado: **`Pendiente de entrevista`**
- ✅ Se crea una `Entrevista` asociada con la Coordinadora
- ✅ El estudiante recibe confirmación

### ¿Quiénes se ven afectados?
- ✅ **Estudiante**: Ve su solicitud en su dashboard con estado "Pendiente de entrevista"
- ✅ **Coordinadora de Inclusión**: Ve la nueva entrevista agendada en su dashboard
- ⏳ **Asesora Técnica**: Aún no ve nada (esperando que Coordinadora complete)
- ⏳ **Director de Carrera**: Aún no ve nada
- ⏳ **Docentes**: Aún no ven nada

---

## 📋 Paso 2: Coordinadora realiza entrevista y anamnesis

### ¿Qué hace la Coordinadora (Encargada de Inclusión)?
1. Ingresa al sistema como **Coordinadora de inclusion**
2. Ve la entrevista agendada en su dashboard
3. Realiza la entrevista con el estudiante
4. Completa la anamnesis (evaluación inicial del caso)
5. Va a "Casos" → Selecciona la solicitud del estudiante
6. Hace clic en **"Informar a CTP"** (botón disponible en la vista)

### ¿Qué sucede en el sistema?
- ✅ El estado de la solicitud cambia a: **`Pendiente de formulación del caso`**
- ✅ Se notifica al sistema que CTP debe revisar

### ¿Quiénes se ven afectados?
- ✅ **Estudiante**: Ve el cambio de estado en su dashboard (opcional, si hay notificación)
- ✅ **Coordinadora**: Ve que el caso cambió de estado
- ✅ **Asesora Pedagógica (AP)**: Puede ver el caso para supervisar (si está asignada)
- ✅ **Asesora Técnica (CTP)**: Ahora puede ver el caso con estado "Pendiente de formulación del caso"
- ⏳ **Director de Carrera**: Aún no ve nada
- ⏳ **Docentes**: Aún no ven nada

---

## 📋 Paso 3: Asesora Técnica (CTP) revisa y formula ajustes

### ¿Qué hace la Asesora Técnica?
1. Ingresa al sistema como **Asesora Tecnica Pedagogica**
2. Ve casos con estado "Pendiente de formulación del caso" en su dashboard
3. Revisa el caso y la información del estudiante
4. Va a "Ajustes" → "Formular ajuste"
5. Crea uno o más **Ajustes Razonables**:
   - Nombre del ajuste
   - Fecha de solicitud
   - Fecha de inicio (opcional)
   - Fecha de término (opcional)
   - Porcentaje de avance (opcional)
   - Selecciona la solicitud asociada
   - Selecciona el estudiante

### ¿Qué sucede en el sistema?
- ✅ Al crear el **primer ajuste**, el estado de la solicitud cambia automáticamente a: **`Pendiente de formulación de ajuste`**
- ✅ Se crea el registro de `AjusteRazonable`
- ✅ La Asesora Técnica puede crear múltiples ajustes razonables

### ¿Quiénes se ven afectados?
- ✅ **Asesora Técnica**: Ve los ajustes creados y el cambio de estado
- ✅ **Estudiante**: El estado de su solicitud cambia (si consulta)
- ✅ **Asesora Pedagógica**: Puede supervisar el proceso
- ⏳ **Director de Carrera**: Aún no ve nada (esperando que CTP envíe)
- ⏳ **Docentes**: Aún no ven nada

---

## 📋 Paso 4: Asesora Técnica envía ajustes a Director

### ¿Qué hace la Asesora Técnica?
1. Una vez formulados todos los ajustes necesarios
2. Va a "Casos" o al detalle de la solicitud
3. Verifica que tenga ajustes razonables asociados
4. Hace clic en **"Enviar a Director"** (botón disponible)

### ¿Qué sucede en el sistema?
- ✅ El estado de la solicitud cambia a: **`Pendiente de Aprobación`**
- ✅ Se asigna automáticamente el Director de Carrera del estudiante
- ✅ El sistema valida que existan ajustes razonables asociados

### ¿Quiénes se ven afectados?
- ✅ **Asesora Técnica**: Ve que el caso fue enviado a Dirección
- ✅ **Director de Carrera**: **AHORA VE EL CASO** en su dashboard con estado "Pendiente de Aprobación"
- ✅ **Estudiante**: El estado cambia a "Pendiente de Aprobación"
- ✅ **Asesora Pedagógica**: Puede ver el seguimiento
- ⏳ **Docentes**: Aún no ven nada (esperando aprobación)

---

## 📋 Paso 5: Director de Carrera revisa y decide

### ¿Qué hace el Director de Carrera?
1. Ingresa al sistema como **Director de carrera**
2. Ve casos con estado "Pendiente de Aprobación" en su dashboard
3. Revisa:
   - Información del estudiante
   - Descripción del caso
   - Todos los ajustes razonables propuestos
   - Evidencias (si las hay)
   - Entrevistas realizadas

### Opciones del Director:

#### A) **APROBAR** la propuesta:
1. Hace clic en **"Aprobar"**
2. Confirma la acción

**¿Qué sucede?**
- ✅ Estado cambia a: **`Aprobado`**
- ✅ Se notifica al Estudiante y Asesora
- ✅ **Se notifica automáticamente a todos los DOCENTES** que tienen asignaturas con ese estudiante
- ✅ Los ajustes quedan oficialmente aprobados

**¿Quiénes se ven afectados?**
- ✅ **Director**: Ve el caso como "Aprobado"
- ✅ **Estudiante**: Recibe notificación de aprobación
- ✅ **Asesora Técnica**: Recibe notificación
- ✅ **Asesora Pedagógica**: Puede ver el seguimiento
- ✅ **DOCENTES**: **AHORA RECIBEN NOTIFICACIÓN** y pueden ver los ajustes aprobados

#### B) **RECHAZAR** la propuesta:
1. Hace clic en **"Rechazar"**
2. Escribe un motivo de rechazo (obligatorio)
3. Confirma

**¿Qué sucede?**
- ✅ Estado cambia a: **`Rechazado`**
- ✅ Se guarda el motivo de rechazo
- ✅ Se notifica al Estudiante y Asesora

**¿Quiénes se ven afectados?**
- ✅ **Director**: Ve el caso como "Rechazado"
- ✅ **Estudiante**: Recibe notificación de rechazo con motivo
- ✅ **Asesora Técnica**: Recibe notificación
- ⏳ **Docentes**: No reciben nada (caso rechazado)

#### C) **DEVOLVER a CTP** para correcciones:
1. Hace clic en **"Devolver a CTP"**
2. Escribe un motivo de devolución (obligatorio)
3. Confirma

**¿Qué sucede?**
- ✅ Estado cambia a: **`Pendiente de formulación de ajuste`**
- ✅ Se guarda el motivo de devolución
- ✅ Se notifica a la Asesora Técnica

**¿Quiénes se ven afectados?**
- ✅ **Director**: El caso vuelve a estar en revisión
- ✅ **Asesora Técnica**: **RECIBE NOTIFICACIÓN** y debe revisar el caso nuevamente
- ⏳ **Docentes**: Aún no ven nada (caso en corrección)

---

## 📋 Paso 6: Docentes visualizan ajustes aprobados

### ¿Qué hacen los Docentes?
1. Ingresan al sistema como **Docente**
2. Reciben **notificación** cuando un ajuste es aprobado (si tienen asignaturas con ese estudiante)
3. Van a "Mis Estudiantes" o Dashboard
4. Ven la lista de estudiantes con ajustes aprobados
5. Pueden ver:
   - Nombre del estudiante
   - Ajustes razonables aprobados
   - Estado de cada ajuste
   - Descripción del caso

### ¿Qué sucede en el sistema?
- ✅ Los docentes pueden consultar los ajustes en cualquier momento
- ✅ Los ajustes aparecen en su dashboard mientras estén activos

### ¿Quiénes se ven afectados?
- ✅ **Docentes**: Pueden ver y aplicar los ajustes aprobados
- ✅ **Estudiante**: Sus ajustes están oficialmente disponibles para docentes
- ✅ **Otros roles**: Pueden seguir viendo el caso como "Aprobado"

---

## 🔄 Caso Especial: Ciclo de Retroalimentación (DC → CTP)

### Si el Director devuelve el caso:
1. **Asesora Técnica** recibe notificación
2. El estado vuelve a: **`Pendiente de formulación de ajuste`**
3. La Asesora Técnica puede:
   - Modificar ajustes existentes
   - Crear nuevos ajustes
   - Eliminar ajustes si es necesario
   - Volver a enviar a Director

### El proceso se repite:
- CTP corrige → Envía a DC → DC revisa → Acepta o vuelve a devolver

---

## 📊 Tabla de Estados y Quién Puede Ver Qué

| Estado | Estudiante | Coordinadora | AP | CTP | Director | Docentes |
|--------|------------|--------------|-----|-----|----------|----------|
| `Pendiente de entrevista` | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| `Pendiente de formulación del caso` | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| `Pendiente de formulación de ajuste` | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| `Pendiente de preaprobación` | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| `Pendiente de Aprobación` | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| `Aprobado` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `Rechazado` | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |

**Leyenda:**
- ✅ Puede ver el caso
- ❌ No puede ver el caso en este estado

---

## 🎯 Flujo Visual Simplificado

```
ESTUDIANTE
   ↓ (1) Solicita entrevista
   ESTADO: "Pendiente de entrevista"
   
COORDINADORA (EI)
   ↓ (2) Realiza entrevista/anamnesis
   ↓ (2) Informa a CTP
   ESTADO: "Pendiente de formulación del caso"
   
ASESORA TÉCNICA (CTP)
   ↓ (3) Revisa caso
   ↓ (3) Crea ajustes razonables
   ESTADO: "Pendiente de formulación de ajuste"
   ↓ (4) Envía a Director
   ESTADO: "Pendiente de Aprobación"
   
DIRECTOR DE CARRERA (DC)
   ↓ (5) Revisa propuesta
   
   OPCIÓN A: APROBAR
   → ESTADO: "Aprobado"
   → NOTIFICA A DOCENTES ✅
   
   OPCIÓN B: RECHAZAR
   → ESTADO: "Rechazado"
   → FIN DEL PROCESO ❌
   
   OPCIÓN C: DEVOLVER
   → ESTADO: "Pendiente de formulación de ajuste"
   → NOTIFICA A CTP
   → VUELVE AL PASO 3 🔄
   
DOCENTES
   ↓ (6) Reciben notificación (si aprobado)
   ↓ (6) Ven ajustes en su dashboard
   ✅ PUEDEN APLICAR LOS AJUSTES
```

---

## ⚠️ Puntos Importantes

1. **El flujo es secuencial**: Cada etapa depende de la anterior
2. **Los estados son obligatorios**: No se puede saltar etapas
3. **Las validaciones están activas**: El sistema verifica que los estados permitan cada acción
4. **Las notificaciones son automáticas**: Se envían cuando corresponde
5. **El ciclo de retroalimentación permite mejoras**: DC puede devolver para correcciones
6. **Solo los docentes con asignaturas del estudiante reciben notificaciones**: Sistema inteligente de filtrado

---

## 🔍 Cómo Probar el Flujo Completo

### Secuencia de Prueba Recomendada:

1. **Crear usuario Estudiante** → Iniciar sesión → Solicitar entrevista
2. **Crear usuario Coordinadora** → Iniciar sesión → Ver entrevista → Informar a CTP
3. **Crear usuario Asesora Técnica** → Iniciar sesión → Ver caso → Crear ajustes → Enviar a Director
4. **Crear usuario Director** → Iniciar sesión → Ver caso → Aprobar/Rechazar/Devolver
5. **Crear usuario Docente** → Asociar con asignaturas del estudiante → Ver ajustes aprobados

### Verificación en cada paso:
- ✅ El estado cambia correctamente
- ✅ Los usuarios correspondientes pueden ver el caso
- ✅ Las notificaciones se envían
- ✅ No se pueden hacer acciones inválidas

---

**¡El sistema está completamente funcional según el diagrama de flujo!** 🎉

