## Resumo rápido (para quem já sabe o básico)

> Se você nunca rodou um projeto Laravel antes, leia primeiro o arquivo `docs/INSTALACAO.md`, também peço que leiam o `docs/MAPA_DO_SISTEMA.md`, aqui encontrarão uma visão geral da Arquitetura e como fizemos o PWA.

```bash
git clone https://github.com/Rafaelalfaia/revista.git
cd revista

cp .env.example .env           # ou copy .env.example .env no Windows
# editar .env se quiser ajustar nome do app etc.

# Criar o banco SQLite
touch database/database.sqlite  # Linux/Mac
# ou: ni database/database.sqlite -ItemType File  # PowerShell

composer install
npm install

php artisan key:generate
php artisan migrate
php artisan storage:link    # opcional

php artisan tinker          # criar usuários

use App\Models\User;

function mk($nome,$email,$senha,$role){
  $u = User::updateOrCreate(
    ['email'=>$email],
    ['name'=>$nome,'password'=>bcrypt($senha),'email_verified_at'=>now()]
  );
  if (method_exists($u,'syncRoles')) $u->syncRoles([$role]);
  return [$u->email, $u->getRoleNames()];
}

mk('Admin',        'admin@admin.com',        'admin',        'Admin');
mk('Coordenador',  'coordenador@coordenador.com', 'coordenador',  'Coordenador');
mk('Revisor',      'revisor@revisor.com',    'revisor',      'Revisor');
mk('Autor',        'autor@autor.com',        'autor',        'Autor');

exit

php artisan serve           # servidor Laravel
npm run dev                 # servidor Vite
```
