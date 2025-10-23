import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useMouseStore = defineStore('mouse', () => {
  const cursorType = ref<null | 'project' | 'editor' | 'action' | 'image'>(null)
  const cursorMessage = ref<string | null>(null)
  const projectClient = ref<string | null>(null)
  const projectDirector = ref<string | null>(null)
  const projectEditor = ref<string | null>(null)
  const cursorImage = ref<string | null>(null)
  const cursorImageAlt = ref<string | null>(null)

  const handleHover = (target: EventTarget | null) => {
    if (!(target instanceof HTMLElement)) return

    cursorType.value =
      (target.dataset.hoverType as 'project' | 'editor' | 'action' | 'image') || null
    projectClient.value = target.dataset.hoverClient || null
    projectDirector.value = target.dataset.hoverDirector || null
    projectEditor.value = target.dataset.hoverEditor || null
    cursorMessage.value = target.dataset.hoverMessage || null
    cursorImage.value = target.dataset.hoverImage || null
    cursorImageAlt.value = target.dataset.hoverImageAlt || null
  }

  const resetHover = () => {
    cursorType.value = null
    cursorMessage.value = null
    projectClient.value = null
    projectDirector.value = null
    projectEditor.value = null
    cursorImage.value = null
    cursorImageAlt.value = null
  }

  return {
    cursorType,
    cursorMessage,
    projectClient,
    projectDirector,
    projectEditor,
    cursorImage,
    cursorImageAlt,
    handleHover,
    resetHover,
  }
})
