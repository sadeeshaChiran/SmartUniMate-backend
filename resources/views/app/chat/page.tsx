'use client'
import { useState, useRef, useEffect } from 'react'
import { Send, Trash2, Bot } from 'lucide-react'

type Message = { role: 'user' | 'assistant'; content: string }

const QUICK = [
  'What modules are in Year 4 IS?',
  'When is the library open?',
  'Who is the HOD of IS department?',
  'Capstone project guidelines?',
]

const SYSTEM = `You are UniMate, a helpful AI assistant for Sabaragamuwa University of Sri Lanka (SUSL).
You assist students with questions about academic modules, campus facilities, library hours (8AM–6PM weekdays),
capstone project guidelines, and general university life. Be concise (2–4 sentences) and friendly.
Current user: R. Abishek, Student ID 22FIS0580, Year 4 Information Systems.`

export default function ChatPage() {
  const [messages, setMessages] = useState<Message[]>([])
  const [input, setInput] = useState('')
  const [loading, setLoading] = useState(false)
  const boxRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (boxRef.current) boxRef.current.scrollTop = boxRef.current.scrollHeight
  }, [messages, loading])

  const send = async (text = input.trim()) => {
    if (!text || loading) return
    setInput('')
    const next: Message[] = [...messages, { role: 'user', content: text }]
    setMessages(next)
    setLoading(true)

    try {
      const res = await fetch('/api/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messages: next, system: SYSTEM }),
      })
      const data = await res.json()
      setMessages([...next, { role: 'assistant', content: data.content }])
    } catch {
      setMessages([...next, { role: 'assistant', content: '⚠️ Connection error. Please try again.' }])
    }
    setLoading(false)
  }

  return (
    <div>
      <h2 className="font-playfair text-2xl font-bold text-[#800000] mb-5">AI Chat Assistant</h2>

      <div className="flex flex-col h-[540px] rounded-2xl border border-stone-200
        dark:border-stone-800 overflow-hidden bg-white dark:bg-stone-900 shadow-sm">

        {/* Header */}
        <div className="flex items-center gap-3 px-5 py-3.5 border-b border-stone-200
          dark:border-stone-800 bg-stone-50 dark:bg-stone-800/50">
          <div className="w-8 h-8 rounded-full bg-[#800000] flex items-center justify-center">
            <Bot size={14} className="text-white" />
          </div>
          <div>
            <p className="text-sm font-semibold">UniMate AI</p>
            <p className="text-xs text-stone-400">Powered by Claude · RAG-enhanced</p>
          </div>
          <button onClick={() => setMessages([])}
            className="ml-auto flex items-center gap-1.5 text-xs text-stone-400
              hover:text-stone-700 dark:hover:text-stone-200 transition px-3 py-1.5
              rounded-lg border border-stone-200 dark:border-stone-700">
            <Trash2 size={12} /> Clear
          </button>
        </div>

        {/* Messages */}
        <div ref={boxRef} className="flex-1 overflow-y-auto p-5 flex flex-col gap-3.5
          bg-stone-50 dark:bg-stone-950">
          <div className="max-w-[78%] px-4 py-2.5 rounded-xl text-sm leading-relaxed
            bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800
            rounded-bl-sm self-start">
            👋 Hello! I&apos;m UniMate, powered by Claude. Ask me anything about SUSL!
          </div>

          {messages.map((m, i) => (
            <div key={i} className={`max-w-[78%] px-4 py-2.5 rounded-xl text-sm leading-relaxed
              ${m.role === 'user'
                ? 'bg-[#800000] text-white self-end rounded-br-sm'
                : 'bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 self-start rounded-bl-sm'
              }`}>
              {m.content}
            </div>
          ))}

          {loading && (
            <div className="max-w-[78%] px-4 py-2.5 rounded-xl text-sm text-stone-400 italic
              bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800
              self-start rounded-bl-sm">
              UniMate is thinking...
            </div>
          )}
        </div>

        {/* Input */}
        <div className="flex gap-2.5 px-4 py-3.5 border-t border-stone-200
          dark:border-stone-800 bg-white dark:bg-stone-900">
          <input value={input} onChange={e => setInput(e.target.value)}
            onKeyDown={e => e.key === 'Enter' && send()}
            placeholder="Ask about IS 4110, library hours, exam schedules..."
            className="flex-1 px-3.5 py-2.5 text-sm rounded-xl border border-stone-200
              dark:border-stone-700 bg-stone-50 dark:bg-stone-800
              focus:outline-none focus:border-[#800000] transition" />
          <button onClick={() => send()} disabled={loading}
            className="w-10 h-10 rounded-xl bg-[#800000] text-white flex items-center
              justify-center hover:bg-[#a01010] transition disabled:opacity-50">
            <Send size={15} />
          </button>
        </div>
      </div>

      {/* Quick prompts */}
      <div className="flex gap-2 mt-3 flex-wrap">
        {QUICK.map(q => (
          <button key={q} onClick={() => send(q)}
            className="text-xs px-3 py-1.5 rounded-lg border border-stone-200
              dark:border-stone-700 hover:bg-stone-100 dark:hover:bg-stone-800 transition">
            {q}
          </button>
        ))}
      </div>
    </div>
  )
}
