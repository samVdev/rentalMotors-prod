<template>
  <section id="requeriments" class="relative w-[90%] mx-auto my-16 rounded-3xl py-20 bg-[#ff7539] text-white">
    <div class="relative max-w-6xl mx-auto px-6 text-center">
      <h2 class="text-4xl md:text-5xl font-extrabold mb-12">
        Requisitos para Aplicar
      </h2>

      <div class="grid md:grid-cols-2 gap-8 text-left">
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">1</span>
          <div>
            <h3 class="text-lg font-semibold">Cédula de ciudadanía</h3>
            <p class="text-sm text-orange-100">Documento oficial vigente para validar identidad.</p>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">2</span>
          <div>
            <h3 class="text-lg font-semibold">Foto tamaño carnet</h3>
            <p class="text-sm text-orange-100">Foto reciente en tamaño carnet.</p>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">3</span>
          <div>
            <h3 class="text-lg font-semibold">Número de teléfono</h3>
            <p class="text-sm text-orange-100">Debe contar con WhatsApp activo.</p>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">4</span>
          <div>
            <h3 class="text-lg font-semibold">Copia de la cédula de ciudadanía al 150%</h3>
            <p class="text-sm text-orange-100">Fotocopia ampliada del documento.</p>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">5</span>
          <div>
            <h3 class="text-lg font-semibold">Recibo de servicio público</h3>
            <p class="text-sm text-orange-100">De agua o de gas, actualizado.</p>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">6</span>
          <div>
            <h3 class="text-lg font-semibold">Dos referencias familiares</h3>
            <p class="text-sm text-orange-100">Incluye copia de cédula, dirección y número de teléfono.</p>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">7</span>
          <div>
            <h3 class="text-lg font-semibold">Dos referencias personales</h3>
            <p class="text-sm text-orange-100">Incluye copia de cédula, dirección y número de teléfono.</p>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">8</span>
          <div>
            <h3 class="text-lg font-semibold">Cuota inicial</h3>
            <p class="text-sm text-orange-100">Monto acordado para iniciar el proceso.</p>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 shadow-lg flex items-start gap-4 hover:bg-white/20 transition">
          <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 text-2xl font-bold">9</span>
          <div>
            <h3 class="text-lg font-semibold">Copia del Permiso PPT al 150% (si es Venezolano)</h3>
            <p class="text-sm text-orange-100">Ampliación del documento.</p>
          </div>
        </div>
      </div>

      <router-link to="/apply" data-aos="fade-right"
        class="block bg-white text-orange-600 font-semibold py-3 px-10 rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition transform mt-12 w-[50%] mx-auto">
        <p>Aplicar</p>
      </router-link>
    </div>
  </section>
</template>
