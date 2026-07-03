<script setup lang="ts">
import 'vue3-carousel/dist/carousel.css'
import { Carousel, Slide } from 'vue3-carousel'

const config = {
  itemsToShow: 1,
  autoplay: 3000,
  wrapAround: true,
  transition: 800,
}

const imagesPC = [
  '/banners/banner_1.png',
  '/banners/banner_2.png',
  '/banners/banner_3.png',
]

const imagesPhone = [
  '/banners/phone/banner_1.jpeg',
  '/banners/phone/banner_2.jpeg',
  '/banners/phone/banner_3.jpeg',
  '/banners/phone/banner_4.jpeg',
  '/banners/phone/banner_5.jpeg',
  '/banners/phone/banner_6.jpeg',
]
</script>

<style scoped>
.carousel-container {
  height: 100%;
}

.slide-item {
  height: 100%;
}

.imgCarrousel {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>

<template>
  <section class="overflow-hidden h-[100vh] md:hidden">
    <Carousel v-bind="config" class="h-full carousel-container">
      <Slide v-for="(img, i) in imagesPhone" :key="i" class="h-full slide-item">
        <img :src="img" alt="" class="imgCarrousel" />
      </Slide>
    </Carousel>
  </section>

  <section class="overflow-hidden h-[100vh] hidden md:block">
    <Carousel v-bind="config" class="h-full carousel-container">
      <Slide v-for="(img, i) in imagesPC" :key="i" class="h-full slide-item">
        <img :src="img" alt="" class="imgCarrousel" />
      </Slide>
    </Carousel>
  </section>
</template>
