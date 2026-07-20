import React, {useState} from 'react';
import { Outlet } from 'react-router-dom';
import './Layout.css';
import Drawer from "@/components/Navigation/Drawer/Drawer.tsx";
import MenuToggle from "@/components/Navigation/MenuToggle/MenuToggle.tsx";

const Layout: React.FC = () => {
  const [isOpened, setIsOpened ] = useState<boolean>(true);

  const toggleDrawer = () => {
    setIsOpened((prev: boolean) => !prev);
  };


  return (
    <div className="Layout">
      <Drawer isOpen={isOpened} onClose={toggleDrawer} />
      <MenuToggle
        onToggle={toggleDrawer}
        isOpen={isOpened}
      />
      <main>
        <Outlet />
      </main>
    </div>
  );
};

export default Layout;