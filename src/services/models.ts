export interface IWine {
  id: number
  name: string
  brand:string
  grape:string
  type: string
  country: string
  region: string
  subregion: string
  amount: number
  segment: string
  wine_stopper: string
  is_organic: number
  other: string
  sku: number
  barcode: string
  active: number
  order: number			
}
export interface IOil {
  id: number
  name: string
  brand:string
  country:string
  region: string
  amount: number
  segment: string
  sku: number
  barcode: string
  active: number
  order: number
}
export interface IDistilled {
  id: number
  name: string
  brand:string
  type:string
  amount: number
  segment: string
  other: string
  sku: number
  barcode: string
  active: number
  order: number
}