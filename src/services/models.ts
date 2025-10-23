export interface IProject {
  id: number
  work_name: string
  client: string
  agency: string
  thumbnail: string[]
  video_url: string
  editor: number
  director: string
  featured: number
}
export interface IEditors {
  id: number
  name: string
  portrait: string
  role: string
  biography: string
  category: string
  slug: string
}
export interface ICategories {
  id: number
  name: string
}
export interface IContact {
  id: number
  name: string
  email: string
  category: number
  type: string
}
export interface IConsulate {
  id: number
  location: string
  gmaps: string
  city: string
  postal: string
  phone: string
  fax: string
  instagram: string
  facebook: string
  twitter: string
}
export interface IAbout {
  id: number
  about: string
  Image: string
}