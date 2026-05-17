'use client'
import { useState } from 'react'
import { Moon, Sun, Bell, LogIn, LogOut } from 'lucide-react'
import { useTheme } from './ThemeProvider'
import NotificationPanel from './NotificationPanel'

export default function Header() {
  const { theme, toggle } = useTheme()
  const [loggedIn, setLoggedIn] = useState(false)
  const [notifOpen, setNotifOpen] = useState(false)

  return (
    <>
      <header className="h-[60px] px-8 flex items-center justify-between sticky top-0 z-50
        bg-white dark:bg-stone-900 border-b border-stone-200 dark:border-stone-800">
        <span className="font-playfair text-xl font-bold text-[#800000] cursor-pointer select-none">
          ◈ Smart UniMate
        </span>

        <div className="flex items-center gap-2">
          <button onClick={toggle}
            className="w-9 h-9 rounded-lg flex items-center justify-center
              text-stone-500 hover:bg-stone-100 dark:hover:bg-stone-800 transition">
            {theme === 'dark' ? <Sun size={16} /> : <Moon size={16} />}
          </button>

          <button onClick={() => setNotifOpen(o => !o)}
            className="w-9 h-9 rounded-lg flex items-center justify-center relative
              text-stone-500 hover:bg-stone-100 dark:hover:bg-stone-800 transition">
            <Bell size={16} />
            <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full
              border-2 border-white dark:border-stone-900" />
          </button>

          <button onClick={() => setLoggedIn(l => !l)}
            className="flex items-center gap-2 px-4 h-[34px] rounded-lg text-sm font-semibold
              text-white bg-[#800000] hover:bg-[#a01010] transition">
            {loggedIn ? <><LogOut size={14} /> Sign Out</> : <><LogIn size={14} /> Sign In</>}
          </button>
        </div>
      </header>

      <NotificationPanel open={notifOpen} onClose={() => setNotifOpen(false)} />
    </>
  )
}
