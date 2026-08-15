import { create } from "zustand";

export const useMainStore = create<any>((set, get) =>({
  isOpenedMenu: false,
  links: [
    { to: '/', label: 'Список' },
    { to: 'Auth', label: 'Авторизация'}
  ],

  toggleMenu: () => {
    const { isOpenedMenu } = get();
    set({ isOpenedMenu: !isOpenedMenu });
  },
}));