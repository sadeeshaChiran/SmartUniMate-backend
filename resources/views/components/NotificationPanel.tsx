'use client'
import { X } from 'lucide-react'

const NOTIFS = [
  { text: 'Capstone proposal deadline in 3 days — IS 4110', time: 'Just now', read: false },
  { text: 'New reply on your community post', time: '1 hour ago', read: false },
  { text: 'Timetable updated for Semester 2', time: 'Yesterday', read: true },
  { text: 'Library closed on 15 April', time: '2 days ago', read: true },
]

export default function NotificationPanel({ open, onClose }: { open: boolean; onClose: () => void }) {
  return (
    <div className={`fixed top-[60px] right-0 w-80 h-[calc(100vh-60px)]
      bg-white dark:bg-stone-900 border-l border-stone-200 dark:border-stone-800
      z-40 overflow-y-auto p-5 transition-transform duration-300
      ${open ? 'translate-x-0' : 'translate-x-full'}`}>

      <div className="flex items-center justify-between mb-4">
        <h3 className="font-semibold text-sm">Notifications</h3>
        <button onClick={onClose} className="text-stone-400 hover:text-stone-700 transition">
          <X size={16} />
        </button>
      </div>

      {NOTIFS.map((n, i) => (
        <div key={i} className="flex gap-3 py-3 border-b border-stone-100 dark:border-stone-800 last:border-0">
          <div className={`w-2 h-2 rounded-full mt-1.5 flex-shrink-0
            ${n.read ? 'bg-stone-300' : 'bg-[#800000]'}`} />
          <div>
            <p className="text-sm leading-snug">{n.text}</p>
            <p className="text-xs text-stone-400 mt-1">{n.time}</p>
          </div>
        </div>
      ))}
    </div>
  )
}
