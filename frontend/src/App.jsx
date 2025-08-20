import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/button.jsx'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card.jsx'
import { Input } from '@/components/ui/input.jsx'
import { Label } from '@/components/ui/label.jsx'
import { Badge } from '@/components/ui/badge.jsx'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs.jsx'
import { Trophy, Star, Crown, Edit, Trash2, Plus, Award, Users, Target } from 'lucide-react'
import { motion, AnimatePresence } from 'framer-motion'
import './App.css'

function App() {
  const [usuario, setUsuario] = useState({ id: 1, nome: 'Usuário Teste', pontos: 0 })
  const [itens, setItens] = useState([])
  const [badges, setBadges] = useState([])
  const [ranking, setRanking] = useState([])
  const [novoItem, setNovoItem] = useState({ nome: '', tipo: '', quantidade: 1 })
  const [editandoItem, setEditandoItem] = useState(null)

  const API_BASE = 'http://localhost/server_gamification.php'

  useEffect(() => {
    carregarDados()
  }, [])

  const carregarDados = async () => {
    try {
      // Carregar itens
      const resItens = await fetch(`${API_BASE}?endpoint=itens`)
      const dadosItens = await resItens.json()
      setItens(dadosItens)

      // Carregar badges do usuário
      const resBadges = await fetch(`${API_BASE}?endpoint=badges&usuario_id=${usuario.id}`)
      const dadosBadges = await resBadges.json()
      setBadges(dadosBadges)

      // Carregar ranking
      const resRanking = await fetch(`${API_BASE}?endpoint=ranking`)
      const dadosRanking = await resRanking.json()
      setRanking(dadosRanking)

      // Atualizar pontos do usuário
      const resUsuario = await fetch(`${API_BASE}?endpoint=usuarios&id=${usuario.id}`)
      const dadosUsuario = await resUsuario.json()
      if (dadosUsuario.pontos !== undefined) {
        setUsuario(prev => ({ ...prev, pontos: dadosUsuario.pontos }))
      }
    } catch (error) {
      console.error('Erro ao carregar dados:', error)
    }
  }

  const criarItem = async (e) => {
    e.preventDefault()
    try {
      const response = await fetch(`${API_BASE}?endpoint=itens`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...novoItem, usuario_id: usuario.id })
      })
      
      if (response.ok) {
        setNovoItem({ nome: '', tipo: '', quantidade: 1 })
        carregarDados()
        mostrarNotificacao('Item criado! +10 pontos!')
      }
    } catch (error) {
      console.error('Erro ao criar item:', error)
    }
  }

  const editarItem = async (item) => {
    try {
      const response = await fetch(`${API_BASE}?endpoint=itens`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...item, usuario_id: usuario.id })
      })
      
      if (response.ok) {
        setEditandoItem(null)
        carregarDados()
        mostrarNotificacao('Item editado! +5 pontos!')
      }
    } catch (error) {
      console.error('Erro ao editar item:', error)
    }
  }

  const deletarItem = async (id) => {
    try {
      const response = await fetch(`${API_BASE}?endpoint=itens`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, usuario_id: usuario.id })
      })
      
      if (response.ok) {
        carregarDados()
        mostrarNotificacao('Item deletado! +2 pontos!')
      }
    } catch (error) {
      console.error('Erro ao deletar item:', error)
    }
  }

  const mostrarNotificacao = (mensagem) => {
    // Implementação simples de notificação
    const notif = document.createElement('div')
    notif.className = 'fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg z-50'
    notif.textContent = mensagem
    document.body.appendChild(notif)
    setTimeout(() => document.body.removeChild(notif), 3000)
  }

  const getBadgeIcon = (icone) => {
    switch (icone) {
      case 'fa-star': return <Star className="w-4 h-4" />
      case 'fa-crown': return <Crown className="w-4 h-4" />
      case 'fa-trophy': return <Trophy className="w-4 h-4" />
      case 'fa-edit': return <Edit className="w-4 h-4" />
      case 'fa-trash': return <Trash2 className="w-4 h-4" />
      default: return <Award className="w-4 h-4" />
    }
  }

  return (
    <div className="frutiger-bg min-h-screen">
      <div className="container mx-auto p-6">
        {/* Header com informações do usuário */}
        <motion.div 
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="glass-card p-6 mb-8 floating-animation"
        >
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-3xl font-bold text-gray-800 mb-2">
                Sistema CRUD Gamificado
              </h1>
              <p className="text-gray-600">Bem-vindo, {usuario.nome}!</p>
            </div>
            <div className="text-center">
              <div className="score-counter pulse-glow">
                {usuario.pontos}
              </div>
              <p className="text-sm text-gray-600">Pontos</p>
            </div>
          </div>
        </motion.div>

        <Tabs defaultValue="itens" className="w-full">
          <TabsList className="grid w-full grid-cols-4 glass-card">
            <TabsTrigger value="itens" className="flex items-center gap-2">
              <Target className="w-4 h-4" />
              Itens
            </TabsTrigger>
            <TabsTrigger value="badges" className="flex items-center gap-2">
              <Award className="w-4 h-4" />
              Badges
            </TabsTrigger>
            <TabsTrigger value="ranking" className="flex items-center gap-2">
              <Users className="w-4 h-4" />
              Ranking
            </TabsTrigger>
            <TabsTrigger value="perfil" className="flex items-center gap-2">
              <Star className="w-4 h-4" />
              Perfil
            </TabsTrigger>
          </TabsList>

          {/* Tab de Itens */}
          <TabsContent value="itens" className="space-y-6">
            {/* Formulário de criação */}
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.1 }}
            >
              <Card className="glass-card">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Plus className="w-5 h-5" />
                    Adicionar Novo Item
                  </CardTitle>
                  <CardDescription>
                    Ganhe 10 pontos por cada item criado!
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <form onSubmit={criarItem} className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                      <Label htmlFor="nome">Nome</Label>
                      <Input
                        id="nome"
                        className="glass-input"
                        value={novoItem.nome}
                        onChange={(e) => setNovoItem({...novoItem, nome: e.target.value})}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="tipo">Tipo</Label>
                      <Input
                        id="tipo"
                        className="glass-input"
                        value={novoItem.tipo}
                        onChange={(e) => setNovoItem({...novoItem, tipo: e.target.value})}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="quantidade">Quantidade</Label>
                      <Input
                        id="quantidade"
                        type="number"
                        min="1"
                        className="glass-input"
                        value={novoItem.quantidade}
                        onChange={(e) => setNovoItem({...novoItem, quantidade: parseInt(e.target.value)})}
                        required
                      />
                    </div>
                    <div className="flex items-end">
                      <Button type="submit" className="glass-button w-full">
                        <Plus className="w-4 h-4 mr-2" />
                        Adicionar
                      </Button>
                    </div>
                  </form>
                </CardContent>
              </Card>
            </motion.div>

            {/* Lista de itens */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
            >
              <Card className="glass-card">
                <CardHeader>
                  <CardTitle>Lista de Itens</CardTitle>
                  <CardDescription>
                    Total de {itens.length} itens cadastrados
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid gap-4">
                    <AnimatePresence>
                      {itens.map((item) => (
                        <motion.div
                          key={item.id}
                          initial={{ opacity: 0, scale: 0.9 }}
                          animate={{ opacity: 1, scale: 1 }}
                          exit={{ opacity: 0, scale: 0.9 }}
                          className="glass-card p-4 flex justify-between items-center"
                        >
                          {editandoItem?.id === item.id ? (
                            <div className="flex gap-2 flex-1">
                              <Input
                                className="glass-input"
                                value={editandoItem.nome}
                                onChange={(e) => setEditandoItem({...editandoItem, nome: e.target.value})}
                              />
                              <Input
                                className="glass-input"
                                value={editandoItem.tipo}
                                onChange={(e) => setEditandoItem({...editandoItem, tipo: e.target.value})}
                              />
                              <Input
                                type="number"
                                className="glass-input"
                                value={editandoItem.quantidade}
                                onChange={(e) => setEditandoItem({...editandoItem, quantidade: parseInt(e.target.value)})}
                              />
                              <Button onClick={() => editarItem(editandoItem)} className="glass-button">
                                Salvar
                              </Button>
                              <Button onClick={() => setEditandoItem(null)} variant="outline">
                                Cancelar
                              </Button>
                            </div>
                          ) : (
                            <>
                              <div>
                                <h3 className="font-semibold">{item.nome}</h3>
                                <p className="text-sm text-gray-600">
                                  {item.tipo} - Quantidade: {item.quantidade}
                                </p>
                              </div>
                              <div className="flex gap-2">
                                <Button
                                  size="sm"
                                  variant="outline"
                                  onClick={() => setEditandoItem(item)}
                                >
                                  <Edit className="w-4 h-4" />
                                </Button>
                                <Button
                                  size="sm"
                                  variant="destructive"
                                  onClick={() => deletarItem(item.id)}
                                >
                                  <Trash2 className="w-4 h-4" />
                                </Button>
                              </div>
                            </>
                          )}
                        </motion.div>
                      ))}
                    </AnimatePresence>
                  </div>
                </CardContent>
              </Card>
            </motion.div>
          </TabsContent>

          {/* Tab de Badges */}
          <TabsContent value="badges">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
            >
              <Card className="glass-card">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Award className="w-5 h-5" />
                    Suas Conquistas
                  </CardTitle>
                  <CardDescription>
                    Você conquistou {badges.length} badges até agora!
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {badges.map((badge) => (
                      <motion.div
                        key={badge.id}
                        initial={{ opacity: 0, scale: 0.8 }}
                        animate={{ opacity: 1, scale: 1 }}
                        className="glass-card p-4 text-center badge-shine"
                      >
                        <div className="text-4xl mb-2 text-yellow-500">
                          {getBadgeIcon(badge.icone)}
                        </div>
                        <h3 className="font-semibold mb-1">{badge.nome}</h3>
                        <p className="text-sm text-gray-600 mb-2">{badge.descricao}</p>
                        <Badge variant="secondary" className="text-xs">
                          {new Date(badge.data_conquista).toLocaleDateString()}
                        </Badge>
                      </motion.div>
                    ))}
                  </div>
                  {badges.length === 0 && (
                    <div className="text-center py-8">
                      <Trophy className="w-16 h-16 mx-auto text-gray-400 mb-4" />
                      <p className="text-gray-600">Nenhuma conquista ainda. Continue usando o sistema para ganhar badges!</p>
                    </div>
                  )}
                </CardContent>
              </Card>
            </motion.div>
          </TabsContent>

          {/* Tab de Ranking */}
          <TabsContent value="ranking">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
            >
              <Card className="glass-card">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Users className="w-5 h-5" />
                    Ranking de Usuários
                  </CardTitle>
                  <CardDescription>
                    Veja como você está se saindo comparado aos outros usuários
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {ranking.map((user, index) => (
                      <motion.div
                        key={user.id}
                        initial={{ opacity: 0, x: -20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ delay: index * 0.1 }}
                        className={`ranking-item glass-card p-4 flex items-center justify-between ${
                          user.id === usuario.id ? 'ring-2 ring-blue-500' : ''
                        }`}
                      >
                        <div className="flex items-center gap-4">
                          <div className="text-2xl font-bold text-gray-500">
                            #{user.posicao}
                          </div>
                          <div>
                            <h3 className="font-semibold">{user.nome}</h3>
                            <p className="text-sm text-gray-600">
                              {user.total_badges} badges conquistados
                            </p>
                          </div>
                        </div>
                        <div className="text-right">
                          <div className="score-counter text-lg">
                            {user.pontos}
                          </div>
                          <p className="text-sm text-gray-600">pontos</p>
                        </div>
                      </motion.div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </motion.div>
          </TabsContent>

          {/* Tab de Perfil */}
          <TabsContent value="perfil">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
            >
              <Card className="glass-card">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Star className="w-5 h-5" />
                    Seu Perfil
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="text-center">
                      <div className="score-counter text-4xl mb-2">
                        {usuario.pontos}
                      </div>
                      <p className="text-gray-600">Pontos Totais</p>
                    </div>
                    <div className="text-center">
                      <div className="text-4xl font-bold text-purple-600 mb-2">
                        {badges.length}
                      </div>
                      <p className="text-gray-600">Badges Conquistados</p>
                    </div>
                    <div className="text-center">
                      <div className="text-4xl font-bold text-blue-600 mb-2">
                        {itens.length}
                      </div>
                      <p className="text-gray-600">Itens Criados</p>
                    </div>
                  </div>
                  
                  <div className="mt-8">
                    <h3 className="text-lg font-semibold mb-4">Sistema de Pontuação</h3>
                    <div className="space-y-2">
                      <div className="flex justify-between items-center p-2 bg-white/20 rounded">
                        <span>Criar item</span>
                        <Badge>+10 pontos</Badge>
                      </div>
                      <div className="flex justify-between items-center p-2 bg-white/20 rounded">
                        <span>Editar item</span>
                        <Badge>+5 pontos</Badge>
                      </div>
                      <div className="flex justify-between items-center p-2 bg-white/20 rounded">
                        <span>Deletar item</span>
                        <Badge>+2 pontos</Badge>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </motion.div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  )
}

export default App

