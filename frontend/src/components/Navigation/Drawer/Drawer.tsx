import { NavLink } from "react-router-dom";
import './Drawer.css'
import Backdrop from "@/components/UI/Backdrop/Backdrop.tsx";
import { useMainStore } from "@/store";

const Drawer = () => {
  const { isOpenedMenu, links, toggleMenu } = useMainStore();

  return (
    <>
      <nav className={`Drawer ${ !isOpenedMenu ? `Drawer--close` : ''}`}>
        <ul>
          {links.map((link: any) => (
            <li key={link.to} onClick={toggleMenu}>
              <NavLink to={link.to}>
                {link.label}
              </NavLink>
            </li>
          ))}
        </ul>
      </nav>
      { isOpenedMenu ? <Backdrop onClick={toggleMenu} /> : null }
    </>
  )
}

export default Drawer;