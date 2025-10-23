import { defineStore } from 'pinia'
import { ref } from 'vue'
import dataService from '@/services/data.services'
import type {
  IProject,
  IEditors,
  ICategories,
  IContact,
  IConsulate,
  IAbout,
} from '@/services/models'
import axios from 'axios'

export const useDataStore = defineStore('data', () => {
  const projects = ref<IProject[]>()
  const editors = ref<IEditors[]>()
  const contactcategories = ref<ICategories[]>()
  const editorcategories = ref<ICategories[]>()
  const contact = ref<IContact[]>()
  const consulate = ref<IConsulate>()
  const about = ref<IAbout>()

  const fetchProjects = async () => {
    try {
      projects.value = await dataService.getProjects()
      return projects.value
    } catch (error) {
      console.error('Error fetching project table: ' + error)
    }
  }

  const fetchEditors = async () => {
    try {
      editors.value = await dataService.getEditors()
      return editors.value
    } catch (error) {
      console.error('Error fetching editors table: ' + error)
    }
  }


  const fetchContactCategories = async () => {
    try {
      contactcategories.value = await dataService.getContactCategories()
      return contactcategories.value
    } catch (error) {
      console.error('Error fetching contact_categories table: ' + error)
    }
  }

  const fetchEditorsCategories = async () => {
    try {
      editorcategories.value = await dataService.getEditorsCategories()
      return editorcategories.value
    } catch (error) {
      console.error('Error fetching editors_cateogries table: ' + error)
    }
  }

  const fetchContact = async () => {
    try {
      contact.value = await dataService.getContact()
      return contact.value
    } catch (error) {
      console.error('Error fetching contact table: ' + error)
    }
  }

  const fetchConsulate = async () => {
    try {
      const data = await dataService.getConsulate()
      if(data) {
        consulate.value = data[0]
      }
      return consulate.value
    } catch (error) {
      console.error('Error fetching consulate table: ' + error)
    }
  }

  const fetchAbout = async () => {
    try {
      const data = await dataService.getAbout()
      if(data) {
        about.value = data[0]
      }
      return about.value
    } catch (error) {
      console.error('Error fetching about table: ' + error)
    }
  }
  const fetchData  = async () => {
    const url = import.meta.env.VITE_APP_ALL_DATA_URL
    
    const fetchPromise = axios
      .get(url)
      .then((res) => {
        return res.data
      })
      .catch((err) => {
        throw err
      })

    return fetchPromise
  }
  // const fetchSelectedLanding = async (slug: string) => {
  //   if (landings.value) {
  //     selectedLanding.value = landings.value.find((it) => it.slug === slug)
  //     return selectedLanding.value
  //   }
  //   try {
  //     const data = await dataService.getLanding()
  //     selectedLanding.value = data.find((it) => it.slug === slug)
  //     return selectedLanding.value
  //   } catch (error) {
  //     console.error('Error fetching selected landing:', error)
  //     return undefined
  //   }
  // }

  return {
    fetchProjects,
    projects,
    fetchEditors,
    editors,
    fetchContactCategories,
    contactcategories,
    fetchEditorsCategories,
    editorcategories,
    fetchContact,
    contact,
    fetchConsulate,
    consulate,
    fetchAbout,
    about,
    fetchData
  }
})
