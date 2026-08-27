export const LINK_VALUE = {
  LIST: 'list',
  AUTH: 'auth',
  LOGOUT: 'logout',
} as const;

export type LinkValue = typeof LINK_VALUE[keyof typeof LINK_VALUE];

export interface Links {
  to: string,
  value: LinkValue
  label: string,
  visible: boolean
}

export interface MainStore {
  isOpenedMenu: boolean,
  toggleMenu: () => void;
}