import React from 'react';
import { Outlet } from 'react-router-dom';
import './Layout.css';
import Drawer from "@/components/Navigation/Drawer/Drawer.tsx";
import MenuToggle from "@/components/Navigation/MenuToggle/MenuToggle.tsx";

const Layout: React.FC = () => {
  return (
    <div className="Layout">
      <Drawer />
      <MenuToggle />
      <main>
        <Outlet />
      </main>
    </div>
  );
};

export default Layout;