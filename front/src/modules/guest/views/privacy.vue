<script setup lang="ts">
import HeaderLanding from '@/layouts/HeaderLanding.vue';

</script>

<template>
    <HeaderLanding />
    <div class="privacy-policy-container">
        <section class="policy-content">
            <h1>Política de Privacidad</h1>
            <p class="last-updated">Última actualización: 19 de marzo de 2026</p>

            <p>
                En nuestra plataforma, la privacidad de nuestros usuarios es una prioridad. Esta Política de Privacidad
                describe cómo recopilamos, utilizamos y protegemos la información personal que nos proporcionas a través
                de nuestro sistema y servicios relacionados.
            </p>

            <hr />

            <h2>1. Información que Recopilamos</h2>
            <p>Recopilamos información que nos proporcionas directamente al interactuar con nuestro sistema, la cual
                incluye:</p>
            <ul>
                <li><strong>Datos de Identificación:</strong> Nombre completo y datos básicos de contacto.</li>
                <li><strong>Datos de Comunicación:</strong> Número de teléfono móvil (utilizado para la mensajería de
                    WhatsApp).</li>
                <li><strong>Información para Evaluación:</strong> Datos necesarios para analizar la viabilidad de
                    otorgamiento de financiación y creación de perfiles de usuario.</li>
            </ul>

            <h2>2. Uso de la Información</h2>
            <p>Utilizamos los datos recolectados exclusivamente para los siguientes fines:</p>
            <ul>
                <li><strong>Análisis de Viabilidad:</strong> Evaluar si el perfil del cliente es apto para los esquemas
                    de financiación ofrecidos.</li>
                <li><strong>Gestión de Usuarios:</strong> Creación, administración y mantenimiento de cuentas dentro de
                    nuestro sistema.</li>
                <li><strong>Comunicación vía WhatsApp:</strong> Envío de notificaciones sobre el estado de solicitudes a través de la API de WhatsApp Business.</li>
            </ul>

            <h2>3. Base Legal para el Tratamiento</h2>
            <p>
                Procesamos tus datos bajo el consentimiento explícito que otorgas al completar nuestros formularios y
                bajo la necesidad de ejecutar medidas precontractuales relacionadas con los servicios de financiación.
            </p>

            <h2>4. Compartición de Datos con Terceros</h2>
            <p>No vendemos ni alquilamos tu información personal. Sin embargo, compartimos datos estrictamente
                necesarios con:</p>
            <ul>
                <li><strong>Meta (WhatsApp):</strong> Para facilitar la entrega de mensajes a través de su
                    infraestructura técnica.</li>
            </ul>

            <h2>5. Seguridad de los Datos</h2>
            <p>
                Implementamos medidas técnicas de seguridad, incluyendo encriptación y protocolos de acceso restringido,
                para evitar el acceso no autorizado, la alteración o la pérdida de tus datos personales en nuestra base
                de datos.
            </p>

            <h2>6. Derechos del Usuario</h2>
            <p>
                Tienes derecho a acceder, rectificar o solicitar la eliminación de tus datos personales de nuestros
                registros. Para ejercer estos derechos o cancelar la recepción de mensajes por WhatsApp, puedes
                contactarnos.
            </p>
        </section>
    </div>
</template>

<style scoped>
.privacy-policy-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
    padding-top: 12vh;
    line-height: 1.6;
    color: #333;
    font-family: sans-serif;
}

.policy-content h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: #1a1a1a;
}

.last-updated {
    font-style: italic;
    color: #666;
    margin-bottom: 2rem;
}

h2 {
    font-size: 1.5rem;
    margin-top: 2rem;
    color: #2c3e50;
    border-left: 4px solid #25d366;
    padding-left: 10px;
}

ul {
    margin-bottom: 1rem;
}

li {
    margin-bottom: 0.5rem;
}

hr {
    border: 0;
    border-top: 1px solid #eee;
    margin: 2rem 0;
}
</style>