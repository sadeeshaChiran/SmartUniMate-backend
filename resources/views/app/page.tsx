'use client'
import Link from 'next/link'
import { Bot, CalendarDays, TrendingUp, Users } from 'lucide-react'

export default function HomePage() {
  return (
    <div>
      <h2 className="font-playfair text-3xl font-bold text-[#800000] mb-1">
        Good morning, Abishek 👋
      </h2>
      <p className="text-stone-500 text-sm mb-8">
        Year 4 · Information Systems · Student ID: 22FIS0580
      </p>

      {/* Stats */}
      <div className="grid grid-cols-3 gap-4 mb-6">
        {[
          { label: 'Current GPA', value: '3.72', sub: 'First Class' },
          { label: 'Credits Earned', value: '98', sub: 'of 120 total' },
          { label: 'Modules This Sem', value: '5', sub: 'Active' },
        ].map(s => (
          <div key={s.label} className="bg-white dark:bg-stone-900 rounded-2xl border
            border-stone-200 dark:border-stone-800 p-5 shadow-sm text-center">
            <p className="text-3xl font-bold text-[#800000]">{s.value}</p>
            <p className="text-xs text-stone-400 mt-1">{s.sub}</p>
            <p className="text-xs font-medium text-stone-600 dark:text-stone-300 mt-0.5">{s.label}</p>
          </div>
        ))}
      </div>

      {/* Today's Classes */}
      <div className="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200
        dark:border-stone-800 p-6 shadow-sm mb-5">
        <h3 className="text-sm font-semibold mb-4">Today&apos;s Classes</h3>
        {[
          { time: '9:00 – 10:00', code: 'IS 4102', room: 'E201' },
          { time: '13:00 – 15:00', code: 'IS 4110', room: 'Lab 3' },
        ].map(c => (
          <div key={c.code} className="flex items-center gap-4 py-2.5 border-b
            border-stone-100 dark:border-stone-800 last:border-0">
            <span className="text-xs text-stone-400 w-28">{c.time}</span>
            <span className="font-semibold text-sm">{c.code}</span>
            <span className="text-xs text-stone-400">{c.room}</span>
          </div>
        ))}
      </div>

      {/* Quick Links */}
      <div className="grid grid-cols-2 gap-4">
        {[
          { href: '/chat',      icon: Bot,          label: 'AI Chat',        desc: 'Ask UniMate anything' },
          { href: '/timetable', icon: CalendarDays, label: 'Timetable',      desc: 'View your schedule' },
          { href: '/gpa',       icon: TrendingUp,   label: 'GPA Calculator', desc: 'Track your grades' },
          { href: '/community', icon: Users,         label: 'Community',      desc: 'Connect with peers' },
        ].map(({ href, icon: Icon, label, desc }) => (
          <Link key={href} href={href}
            className="flex items-center gap-4 p-5 rounded-2xl border border-stone-200
              dark:border-stone-800 bg-white dark:bg-stone-900 shadow-sm
              hover:border-[#800000]/30 hover:bg-red-50/30 dark:hover:bg-red-950/10 transition">
            <div className="w-10 h-10 rounded-xl bg-[#800000]/10 flex items-center justify-center">
              <Icon size={18} className="text-[#800000]" />
            </div>
            <div>
              <p className="text-sm font-semibold">{label}</p>
              <p className="text-xs text-stone-400">{desc}</p>
            </div>
          </Link>
        ))}
      </div>
    </div>
  )
}
