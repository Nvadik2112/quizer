import {NavLink} from "react-router-dom";
import './Drawer.css'
import Backdrop from "@/components/UI/Backdrop/Backdrop.tsx";


interface DrawerProps {
  isOpen: boolean,
  onClose: () => void;
}

const Drawer = (props: DrawerProps) => {
  const links = [
    { to: '/', label: 'Список' },
    { to: 'Auth', label: 'Авторизация'}
  ]

  return (
    <>
      <nav className={`Drawer ${ !props.isOpen ? `Drawer--close` : ''}`}>
        <ul>
          {links.map((link) => (
            <li key={link.to} onClick={props.onClose}>
              <NavLink to={link.to}>
                {link.label}
              </NavLink>
            </li>
          ))}
        </ul>
      </nav>
      { props.isOpen ? <Backdrop onClick={props.onClose} /> : null }
    </>
  )
}

export default Drawer;