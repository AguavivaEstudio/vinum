import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useMouseStore = defineStore('mouse', () => {
  const contentStatus = ref<boolean>(false)

  const handleContentStatus = (cst: boolean) => {
    contentStatus.value = cst
  }
  return { contentStatus, handleContentStatus }
})
