import React from 'react';
import { Outlet } from 'react-router-dom';
import './Layout.css';

const Layout: React.FC = () => {
  return (
    <div className="Layout">
      <main>
        <Outlet />
      </main>
    </div>
  );
};

export default Layout;