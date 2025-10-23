import { ref } from 'vue'
import { defineStore } from 'pinia'
import { useRoute } from 'vue-router'

export const useLoadingScreenStore = defineStore('loading', () => {
  const route = useRoute()

  const num = ref<number>(0)
  const isCached = ref<boolean>(false)
  const routes = ref<string[]>([])

  const easeInOutCubic = (t: number): number =>
    t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2

  const startCountUp = () => {
    if (routes.value.includes(route.path)) {
      isCached.value = true
    } else {
      routes.value = [...routes.value, route.path]
      isCached.value = true
    }
    const duration = isCached.value ? 2000 : 3000
    const start = performance.now()

    const animate = (now: number) => {
      const elapsed = now - start
      const progress = Math.min(elapsed / duration, 1)
      num.value = Math.floor(easeInOutCubic(progress) * 100)

      if (progress < 1) {
        requestAnimationFrame(animate)
      }
    }

    requestAnimationFrame(animate)
  }

  return {
    num,
    isCached,
    startCountUp,
  }
})
