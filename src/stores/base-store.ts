import { defineStore } from 'pinia'
import { ref } from 'vue'
import dataService from '@/services/data.services'
import type {
  IWine,
  IOil,
  IDistilled
} from '@/services/models'
import axios from 'axios'

export const useDataStore = defineStore('data', () => {
  const wines = ref<IWine[]>()
  const oils = ref<IOil[]>()
  const distillates = ref<IDistilled[]>()

  const fetchWines = async () => {
    try {
      wines.value = await dataService.getWines()
      return wines.value
    } catch (error) {
      console.error('Error fetching wines table: ' + error)
    }
  }
  const fetchOils = async () => {
    try {
      oils.value = await dataService.getOils()
      return oils.value
    } catch (error) {
      console.error('Error fetching oils table: ' + error)
    }
  }
  const fetchDistillates = async () => {
    try {
      distillates.value = await dataService.getDistillates()
      return distillates.value
    } catch (error) {
      console.error('Error fetching distillates table: ' + error)
    }
  }

  return {
    fetchWines,
    wines,
    fetchOils,
    oils,
    fetchDistillates,
    distillates
  }
})
