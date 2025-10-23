import type {
  IWine,
  IOil,
  IDistilled
} from '@/services/models'

import axios from 'axios';


class DataService {
  baseURL: string
  private dataCollection = new Map<string, any>()

  constructor() {
    this.baseURL = "https://panelconsulate.aguaviva.com.ar/api/"
  }

  getWines(): Promise<IWine[]> {
    return this.getData('wines')
  }

  getOils(): Promise<IOil[]> {
    return this.getData('oils')
  }

  getDistillates(): Promise<IDistilled[]> {
    return this.getData('distillates')
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