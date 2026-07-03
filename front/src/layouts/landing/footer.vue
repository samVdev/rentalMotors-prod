<script setup lang="ts">
import { menuLanding } from '@/utils/constantes/menuLanding';

</script>

<template>
 <footer className="bg-[#FF7539] text-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8">
          <div className="md:col-span-4 space-y-4">
            <div className="flex items-center">
              <h2 className="text-2xl font-semibold tracking-tight">YUTAJE</h2>
            </div>
            <p className="text-white/80 max-w-xs">
                Simplifica la administración del condominio con una plataforma que conecta residentes y administradores.
            </p>
            <div className="flex flex-col space-y-2 pt-2">
              <div className="flex items-center space-x-2 text-white/80 hover:text-white transition-colors">
                <MapPin size={18} />
                <span>Av. Sucre, entre la 2ta y 4ta transversal. Los Dos Caminos, Caracas, Venezuela</span>
              </div>
              <div className="flex items-center space-x-2 text-white/80 hover:text-white transition-colors">
                <Phone size={18} />
                <span>0424-0000000</span>
              </div>
            </div>
          </div>
          <div className="md:col-span-3 md:col-start-7">
            <h3 className="text-lg font-medium mb-4">Links</h3>
            <nav className="flex flex-col space-y-3">
              <a :href="menu.to" className="text-white/80 hover:text-white transition-colors" v-for="menu in menuLanding">{{ menu.name }}</a>
              <router-link to="/login" className="text-white/80 hover:text-white transition-colors">Login</router-link>
            </nav>
          </div>
          <div className="md:col-span-3">
            <h3 className="text-lg font-medium mb-4">Ingresa al Sistema</h3>
            <div className="relative">
              <router-link to="/login" className="mt-3 w-full bg-white block text-center text-[#FF7539] px-4 py-2 rounded-md font-medium hover:bg-white/90 transition-colors">
                Iniciar sesión
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </footer>
    </template>