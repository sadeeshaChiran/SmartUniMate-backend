'use client';

import { useState, useRef, useEffect } from "react";

// ─── Types ─────────────────────────────────────
interface Message {
  id: number;
  role: "bot" | "user";
  text: string;
  timestamp?: string;
}

// ─── Quick Chips ──────────────────────────────
const QUICK_CHIPS = [
  "Course Info",
  "Campus Map",
  "Library Hours",
  "Exam Dates",
];

// ─── Initial Message ──────────────────────────
const INITIAL_MESSAGE: Message = {
  id: 1,
  role: "bot",
  text: "Hello 👋 I'm UniMate AI. Ask me anything about SUSL.",
};

export default function Chat() {
  const [messages, setMessages] = useState<Message[]>([INITIAL_MESSAGE]);
  const [input, setInput] = useState("");
  const [loading, setLoading] = useState(false);

  const bottomRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages, loading]);

  // ─── Send Message ────────────────────────────
  const sendMessage = async (text: string) => {
    const trimmed = text.trim();
    if (!trimmed || loading) return;

    const userMsg: Message = {
      id: Date.now(),
      role: "user",
      text: trimmed,
    };

    setMessages((prev) => [...prev, userMsg]);
    setInput("");
    setLoading(true);

    try {
      const res = await fetch('/api/chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          message: trimmed,
          messages: [...messages, userMsg],
        }),
      });

      if (!res.ok) throw new Error("Request failed");

      const data = await res.json();

      const botMsg: Message = {
        id: Date.now() + 1,
        role: "bot",
        text: data.reply,
        timestamp: new Date().toISOString(),
      };

      setMessages((prev) => [...prev, botMsg]);

    } catch (err) {
      setMessages((prev) => [
        ...prev,
        {
          id: Date.now() + 1,
          role: "bot",
          text: "⚠️ Server error. Try again later.",
        },
      ]);
    } finally {
      setLoading(false);
    }
  };

  // ─── UI ─────────────────────────────────────
  return (
    <div className="flex flex-col h-screen bg-gray-50">

      {/* Header */}
      <div className="p-4 bg-white border-b font-bold text-red-900">
        UniMate AI Assistant
      </div>

      {/* Messages */}
      <div className="flex-1 overflow-y-auto p-4 space-y-3">
        {messages.map((m) => (
          <div
            key={m.id}
            className={`max-w-[75%] px-4 py-2 rounded-lg text-sm
            ${m.role === "user"
                ? "ml-auto bg-red-900 text-white"
                : "bg-white border"
              }`}
          >
            {m.text}
          </div>
        ))}

        {loading && (
          <div className="text-sm text-gray-500">Thinking...</div>
        )}

        <div ref={bottomRef} />
      </div>

      {/* Quick chips */}
      <div className="flex gap-2 p-2 border-t bg-white flex-wrap">
        {QUICK_CHIPS.map((chip) => (
          <button
            key={chip}
            onClick={() => sendMessage(chip)}
            className="px-3 py-1 text-xs border rounded-full hover:bg-gray-100"
          >
            {chip}
          </button>
        ))}
      </div>

      {/* Input */}
      <div className="flex gap-2 p-3 border-t bg-white">
        <input
          className="flex-1 border rounded px-3 py-2 text-sm"
          value={input}
          onChange={(e) => setInput(e.target.value)}
          onKeyDown={(e) => e.key === "Enter" && sendMessage(input)}
          placeholder="Ask something..."
        />

        <button
          onClick={() => sendMessage(input)}
          disabled={loading}
          className="bg-red-900 text-white px-4 rounded disabled:opacity-50"
        >
          Send
        </button>
      </div>
    </div>
  );
}