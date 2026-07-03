'use client'
import { useState, useCallback } from 'react'
import { Plus, Trash2 } from 'lucide-react'

type Mod = { name: string; credits: number; grade: string }

const GRADES: Record<string, number> = {
  'A+': 4.0, 'A': 4.0, 'A-': 3.7, 'B+': 3.3, 'B': 3.0,
  'B-': 2.7, 'C+': 2.3, 'C': 2.0, 'C-': 1.7, 'D': 1.0, 'F': 0.0,
}

const DEFAULT_MODS: Mod[] = [
  { name: 'IS 4110 Capstone', credits: 6, grade: 'A' },
  { name: 'IS 4102 Data Mining', credits: 3, grade: 'B+' },
  { name: 'IS 4104 Cloud Computing', credits: 3, grade: 'A-' },
  { name: 'IS 4106 Research Methods', credits: 2, grade: 'A' },
]

function calcGPA(mods: Mod[]) {
  const tc = mods.reduce((a, m) => a + m.credits, 0)
  const tp = mods.reduce((a, m) => a + (GRADES[m.grade] ?? 0) * m.credits, 0)
  return { gpa: tc > 0 ? tp / tc : 0, tc, tp }
}

function classify(gpa: number) {
  if (gpa >= 3.7) return '🎓 First Class Honours'
  if (gpa >= 3.3) return 'Upper Second Class'
  if (gpa >= 3.0) return 'Second Class'
  if (gpa >= 2.0) return 'Pass'
  return gpa > 0 ? 'Fail' : '—'
}

export default function GpaPage() {
  const [mods, setMods] = useState<Mod[]>(DEFAULT_MODS)
  const update = useCallback((i: number, field: keyof Mod, val: string | number) => {
    setMods(prev => prev.map((m, idx) => idx === i ? { ...m, [field]: val } : m))
  }, [])
  const remove = (i: number) => setMods(prev => prev.filter((_, idx) => idx !== i))
  const add = () => setMods(prev => [...prev, { name: 'New Module', credits: 3, grade: 'B' }])

  const { gpa, tc, tp } = calcGPA(mods)
  const pct = (gpa / 4) * 100

  return (
    <div>
      <h2 className="font-playfair text-2xl font-bold text-[#800000] mb-5">GPA Calculator</h2>

      <div className="grid grid-cols-2 gap-5">
        {/* Module table */}
        <div className="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200
          dark:border-stone-800 p-6 shadow-sm">
          <h3 className="text-sm font-semibold mb-4">Module Grades</h3>

          <div className="grid grid-cols-[1fr_70px_80px_40px] gap-2 text-xs
            text-stone-400 font-semibold mb-2 px-1">
            <span>Module</span><span>Credits</span><span>Grade</span><span></span>
          </div>

          {mods.map((m, i) => (
            <div key={i} className="grid grid-cols-[1fr_70px_80px_40px] gap-2 mb-2 items-center">
              <input value={m.name} onChange={e => update(i, 'name', e.target.value)}
                className="text-xs px-2 py-1.5 rounded-lg border border-stone-200
                  dark:border-stone-700 bg-stone-50 dark:bg-stone-800 w-full
                  focus:outline-none focus:border-[#800000]" />
              <input type="number" value={m.credits} min={1} max={12}
                onChange={e => update(i, 'credits', +e.target.value)}
                className="text-xs px-2 py-1.5 rounded-lg border border-stone-200
                  dark:border-stone-700 bg-stone-50 dark:bg-stone-800 w-full
                  focus:outline-none focus:border-[#800000]" />
              <select value={m.grade} onChange={e => update(i, 'grade', e.target.value)}
                className="text-xs px-2 py-1.5 rounded-lg border border-stone-200
                  dark:border-stone-700 bg-stone-50 dark:bg-stone-800 w-full
                  focus:outline-none focus:border-[#800000]">
                {Object.keys(GRADES).map(g => <option key={g}>{g}</option>)}
              </select>
              <button onClick={() => remove(i)}
                className="text-stone-400 hover:text-red-500 transition flex justify-center">
                <Trash2 size={14} />
              </button>
            </div>
          ))}

          <button onClick={add}
            className="mt-3 w-full flex items-center justify-center gap-2 py-2 text-xs
              rounded-xl border border-dashed border-stone-300 dark:border-stone-700
              text-stone-500 hover:bg-stone-50 dark:hover:bg-stone-800 transition">
            <Plus size={13} /> Add Module
          </button>
        </div>

        {/* GPA display */}
        <div className="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200
          dark:border-stone-800 p-6 shadow-sm flex flex-col items-center">
          <div className="relative w-36 h-36 mb-4">
            <svg className="w-full h-full -rotate-90" viewBox="0 0 120 120">
              <circle cx="60" cy="60" r="52" fill="none" stroke="#f1ece8" strokeWidth="12" />
              <circle cx="60" cy="60" r="52" fill="none" stroke="#800000" strokeWidth="12"
                strokeDasharray={`${2 * Math.PI * 52}`}
                strokeDashoffset={`${2 * Math.PI * 52 * (1 - pct / 100)}`}
                strokeLinecap="round" className="transition-all duration-500" />
            </svg>
            <div className="absolute inset-0 flex flex-col items-center justify-center">
              <span className="text-3xl font-bold text-[#800000]">{gpa.toFixed(2)}</span>
              <span className="text-xs text-stone-400">/ 4.00</span>
            </div>
          </div>

          <p className="text-sm text-stone-500 mb-6">{classify(gpa)}</p>

          <div className="grid grid-cols-2 gap-3 w-full">
            <div className="bg-stone-50 dark:bg-stone-800 rounded-xl p-4 text-center">
              <p className="text-2xl font-bold text-[#800000]">{tc}</p>
              <p className="text-xs text-stone-400 mt-0.5">Total Credits</p>
            </div>
            <div className="bg-stone-50 dark:bg-stone-800 rounded-xl p-4 text-center">
              <p className="text-2xl font-bold text-[#800000]">{tp.toFixed(1)}</p>
              <p className="text-xs text-stone-400 mt-0.5">Grade Points</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}