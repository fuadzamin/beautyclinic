import { createContext } from 'reka-ui'

export const SIDEBAR_COOKIE_NAME = 'sidebar:state'
export const SIDEBAR_COOKIE_MAX_AGE = 60 * 60 * 24 * 7
export const SIDEBAR_WIDTH = '18rem'
export const SIDEBAR_WIDTH_MOBILE = '18rem'
export const SIDEBAR_WIDTH_ICON = '2.5rem'
export const SIDEBAR_KEYBOARD_SHORTCUT = 'b'

export const [useSidebar, provideSidebar] = createContext('SidebarProvider')
