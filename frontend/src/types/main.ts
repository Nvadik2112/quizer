export interface Links {
  to: string,
  label: string,
}

export interface MainStore {
  isOpenedMenu: boolean,
  links: Links[],
  toggleMenu: () => void;
}