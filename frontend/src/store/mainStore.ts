import { create } from "zustand";
import type { MainStore } from "@/types/main.ts";

export const useMainStore = create<MainStore>()(
  (set, get) =>({
    isOpenedMenu: false,
    toggleMenu: () => {
      const { isOpenedMenu } = get();
      set({ isOpenedMenu: !isOpenedMenu });
    },
  })
);