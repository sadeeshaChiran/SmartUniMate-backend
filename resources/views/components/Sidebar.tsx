'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import {
  Home,
  Bot,
  Book,
  CalendarDays,
  TrendingUp,
  Users,
  User,
  ShieldAlert
} from 'lucide-react'

const NAV = [
  { href: '/', icon: Home, label: 'Home' },
  { href: '/chat', icon: Bot, label: 'AI Chat' },
  { href: '/academic', icon: Book, label: 'Academic' },
  { href: '/timetable', icon: CalendarDays, label: 'Timetable' },
  { href: '/gpa', icon: TrendingUp, label: 'GPA Calculator' },
  { href: '/community', icon: Users, label: 'Community' },
  { href: '/profile', icon: User, label: 'Profile' },
  { href: '/admin', icon: ShieldAlert, label: 'Admin', danger: true }
]

export default function Sidebar() {
  const path = usePathname()

  return (
    <aside className="w-[220px] min-w-[220px] sticky top-[60px] h-[calc(100vh-60px)] overflow-y-auto px-3 py-5 bg-white dark:bg-stone-900 border-r border-stone-200 dark:border-stone-800">

      {/* Title */}
      <p className="text-[10px] font-semibold uppercase tracking-widest text-stone-400 px-3 mb-2">
        Main
      </p>

      {/* Navigation */}
      {NAV.map(({ href, icon: Icon, label, danger }) => {
        const active = path === href

        return (
          <Link
            key={href}
            href={href}
            className={`flex items-center gap-2.5 px-3 py-2.5 rounded-xl mb-1 text-sm font-medium transition
              ${
                active
                  ? 'bg-red-50 dark:bg-red-950/40 text-[#800000]'
                  : danger
                  ? 'text-red-500 hover:bg-stone-100 dark:hover:bg-stone-800'
                  : 'text-stone-700 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-800'
              }`}
          >
            <Icon size={18} />
            <span>{label}</span>
          </Link>
        )
      })}
    </aside>
  )
}