<template>
  <section class="relative h-[56vh] md:h-[64vh] w-full overflow-hidden rounded-b-[var(--radius-lg)]">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1528640930834-04394d6b76df?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
    <div class="relative z-10 h-full flex items-end">
      <div class="container-px mx-auto pb-10">
        <h1 class="text-4xl md:text-6xl font-bold text-white">ExploreBenin<span class="text-[color:var(--color-secondary)]">360</span></h1>
        <p class="mt-2 text-white/90 text-lg">{{ t('brand.baseline') }}</p>
        <div class="mt-6 flex gap-3">
          <Button variant="primary" size="lg">
            {{ t('hero.cta') }}
          </Button>
          <Button variant="outline" size="lg">
            <span class="flex items-center gap-2">{{ t('nav.destinations') }} <Icon name="ArrowRight"/></span>
          </Button>
        </div>
      </div>
    </div>
  </section>

  <main class="container-px mx-auto py-10 space-y-12">
    <section>
      <h2 class="text-2xl font-bold mb-4">{{ t('sections.popular_destinations') }}</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card v-for="i in 6" :key="i">
          <template #title>Destination {{ i }}</template>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit.
          <template #actions>
            <Button variant="secondary" size="sm">{{ t('hero.cta') }}</Button>
          </template>
        </Card>
      </div>
    </section>

    <section>
      <h2 class="text-2xl font-bold mb-4">{{ t('sections.must_do') }}</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <Badge v-for="i in 12" :key="i">Tag {{ i }}</Badge>
      </div>
    </section>

    <section>
      <h2 class="text-2xl font-bold mb-4">{{ t('sections.testimonials') }}</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Alert v-for="i in 3" :key="i" type="info">“Great trip {{ i }}”</Alert>
      </div>
    </section>
  </main>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import Button from '@/components/ui/Button.vue'
import Card from '@/components/ui/Card.vue'
import Badge from '@/components/ui/Badge.vue'
import Alert from '@/components/ui/Alert.vue'
import Icon from '@/components/ui/Icon.vue'

const { t } = useI18n()
</script>
