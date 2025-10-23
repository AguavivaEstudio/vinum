import { ref } from 'vue'
import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'

export const usePageTransitionStore = defineStore('pagetr', () => {
  const router = useRouter()
  const customRoute = ref<string>('/')
  const status = ref<'idle' | 'preparing' | 'routing'>('idle')

  const handleRoute = (slug: string) => {
    status.value = 'preparing'

    setTimeout(() => {
      status.value = 'routing'
      router.push(slug)

      window.scrollTo(0, 0)
    }, 1000)
    setTimeout(() => {
      status.value = 'idle'
    }, 2000)
  }

  return { customRoute, status, handleRoute }
})
