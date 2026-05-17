'use client'
import { useState } from 'react'

const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']
const SLOTS = ['8:00', '9:00', '10:00', '11:00', '13:00', '14:00', '15:00']
type Entry = { code: string; room: string }
type TT = Record<string, Entry>

const DEFAULT_TT: TT = {
  'Mon-1': { code: 'IS 4110', room: 'Lab 3' },
  'Tue-4': { code: 'IS 4102', room: 'E201' },
  'Thu-2': { code: 'IS 4104', room: 'B105' },
  'Fri-0': { code: 'IS 4106', room: 'A201' },
}

export default function TimetablePage() {
  const [tt, setTT] = useState<TT>(DEFAULT_TT)
  const [modal, setModal] = useState<{ day: string; slot: number } | null>(null)
  const [form, setForm] = useState({ code: '', room: '' })

  const save = () => {
    if (!modal || !form.code) return
    setTT(prev => ({ ...prev, [`${modal.day}-${modal.slot}`]: { ...form } }))
    setModal(null)
    setForm({ code: '', room: '' })
  }

  return (
    <div>
      <h2 className="font-playfair text-2xl font-bold text-[#800000] mb-5">My Timetable</h2>

      <div className="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200
        dark:border-stone-800 p-6 shadow-sm overflow-x-auto">

        <div className="grid gap-1" style={{ gridTemplateColumns: '72px repeat(5, 1fr)' }}>
          {/* Header row */}
          <div />
          {DAYS.map(d => (
            <div key={d} className="bg-[#800000] text-white text-xs font-semibold
              text-center py-2 rounded-lg">{d}</div>
          ))}

          {/* Slot rows */}
          {SLOTS.map((slot, si) => (
            <>
              <div key={slot} className="flex items-center justify-center text-xs
                text-stone-400 font-medium bg-stone-50 dark:bg-stone-800 rounded-lg py-2">
                {slot}
              </div>
              {DAYS.map(day => {
                const key = `${day}-${si}`
                const entry = tt[key]
                return (
                  <div key={key}
                    onClick={() => { setModal({ day, slot: si }); if (entry) setForm(entry) }}
                    className={`min-h-[52px] rounded-lg p-2 cursor-pointer transition text-xs
                      ${entry
                        ? 'bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 hover:bg-red-100'
                        : 'border border-dashed border-stone-200 dark:border-stone-700 hover:bg-stone-50 dark:hover:bg-stone-800'
                      }`}>
                    {entry && <>
                      <p className="font-bold text-[#800000]">{entry.code}</p>
                      <p className="text-stone-400 text-[10px] mt-0.5">{entry.room}</p>
                    </>}
                  </div>
                )
              })}
            </>
          ))}
        </div>
      </div>

      {/* Modal */}
      {modal && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
          <div className="bg-white dark:bg-stone-900 rounded-2xl p-7 w-96 shadow-2xl">
            <h3 className="font-semibold mb-5">{modal.day} · {SLOTS[modal.slot]}:00</h3>
            <label className="text-xs text-stone-500 mb-1 block">Module Code</label>
            <input value={form.code} onChange={e => setForm(f => ({ ...f, code: e.target.value }))}
              placeholder="e.g. IS 4110"
              className="w-full mb-3 px-3 py-2 text-sm rounded-xl border border-stone-200
                dark:border-stone-700 bg-stone-50 dark:bg-stone-800 focus:outline-none focus:border-[#800000]" />
            <label className="text-xs text-stone-500 mb-1 block">Room</label>
            <input value={form.room} onChange={e => setForm(f => ({ ...f, room: e.target.value }))}
              placeholder="e.g. Lab 3, E201"
              className="w-full mb-5 px-3 py-2 text-sm rounded-xl border border-stone-200
                dark:border-stone-700 bg-stone-50 dark:bg-stone-800 focus:outline-none focus:border-[#800000]" />
            <div className="flex gap-2">
              <button onClick={save}
                className="flex-1 py-2 rounded-xl bg-[#800000] text-white text-sm font-semibold hover:bg-[#a01010] transition">
                Save
              </button>
              <button onClick={() => setModal(null)}
                className="flex-1 py-2 rounded-xl border border-stone-200 dark:border-stone-700 text-sm hover:bg-stone-50 dark:hover:bg-stone-800 transition">
                Cancel
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
