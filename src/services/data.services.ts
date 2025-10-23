import type {
  IProject,
  IEditors,
  ICategories,
  IContact,
  IConsulate,
  IAbout
} from '@/services/models'

import axios from 'axios';


class DataService {
  baseURL: string
  private dataCollection = new Map<string, any>()

  constructor() {
    this.baseURL = import.meta.env.VITE_APP_SCHEMA_API_URL
  }

  getProjects(): Promise<IProject[]> {
    return this.getData('projects')
  }

  getEditors(): Promise<IEditors[]> {
    return this.getData('editors')
  }

  getEditorsCategories(): Promise<ICategories[]> {
    return this.getData('editors_cateogries')
  }

  getContactCategories(): Promise<ICategories[]> {
    return this.getData('contact_categories')
  }

  getContact(): Promise<IContact[]> {
    return this.getData('contact')
  }

  getConsulate(): Promise<IConsulate[]> {
    return this.getData('consulate')
  }

  getAbout(): Promise<IAbout[]> {
    return this.getData('about')
  }

  private getData(table: string): Promise<any[]> {
    const uri = `${table}`

    if (this.dataCollection.has(table)) {
      return this.dataCollection.get(table)!
    }

    const fetchPromise = axios
      .get(this.baseURL.concat(uri))
      .then((res) => {
        this.dataCollection.set(table, Promise.resolve(res.data))
        return res.data
      })
      .catch((err) => {
        this.dataCollection.delete(table)
        throw err
      })

    this.dataCollection.set(table, fetchPromise)

    return fetchPromise
  }
}

const dataService = new DataService()
export default dataService