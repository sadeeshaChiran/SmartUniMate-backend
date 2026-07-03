import type { Metadata } from 'next'
import { DM_Sans, Playfair_Display } from 'next/font/google'
import './globals.css'
import Sidebar from '../components/Sidebar'
import Header from '../components/Header'

const dmSans = DM_Sans({ subsets: ['latin'], variable: '--font-dm-sans' })
const playfair = Playfair_Display({ subsets: ['latin'], variable: '--font-playfair' })

export const metadata: Metadata = {
  title: 'Smart UniMate — Sabaragamuwa University',
  description: 'AI-powered digital campus assistant for SUSL students',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body className={`${dmSans.variable} ${playfair.variable} font-sans`}>

        {/* Header */}
        <Header />

        {/* Layout */}
        <div className="flex">
          <Sidebar />

          <main className="flex-1 p-5">
            {children}
          </main>
        </div>

      </body>
    </html>
  )
}