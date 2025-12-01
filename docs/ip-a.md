Ton IP locale est clairement identifiable.

---

## ✅ IP à utiliser

Interface Wi-Fi **`wlp5s0`** :

```
192.168.1.7
```

---

## ✅ Commande à lancer sur ton PC

Assure-toi que ton serveur Laravel est démarré ainsi :

```bash
php artisan serve --host=0.0.0.0 --port=8001
```

⚠️ **Ne pas utiliser `127.0.0.1` ni `localhost`.**

---

## ✅ Accès depuis ton téléphone

Conditions :

* téléphone **sur le même Wi-Fi**
* aucun VPN actif

Dans le navigateur du téléphone, ouvre :

```
http://192.168.1.7:8001
```

---

## ✅ Test rapide (optionnel depuis le PC)

Dans un navigateur du PC :

```
http://192.168.1.7:8001
```

Si ça marche sur le PC → ça marchera sur le téléphone.

---

## 🚫 Si ça ne marche pas

### Pare-feu (Linux)

```bash
sudo ufw status
sudo ufw allow 8001
sudo ufw reload
```

Ou test rapide (temporaire) :

```bash
sudo ufw disable
```

---

## ✅ Résumé ultra-court

* IP : `192.168.1.7`
* Port : `8001`
* URL téléphone :

```
http://192.168.1.7:8001
```

C’est la bonne configuration.

=========================================================


---

## 1️⃣ Message sur le téléphone : **connexion sécurisée**

> *« Ce site ne peut pas fournir de connexion sécurisée »*

### Cause exacte

Ton **téléphone essaie d’ouvrir le site en HTTPS** alors que :

* `php artisan serve` **ne sert que du HTTP**
* aucune SSL locale n’est configurée

Le message *“réponse incorrecte”* vient de là.

### ✅ Correction

Sur le téléphone, tape **explicitement** :

```
http://192.168.1.7:8001
```

⚠️ **Pas https**
⚠️ Ne pas laisser le navigateur auto-corriger

Si besoin :

* vide l’URL
* tape tout à la main
* désactive “toujours utiliser HTTPS” (Chrome mobile)

---

## 2️⃣ CSS non chargé sur PC (et téléphone)

Problème **Vite / npm**.

### Cause exacte

`npm run dev` expose Vite sur :

```
http://localhost:5173
```

Mais :

* `localhost` depuis le téléphone = **le téléphone lui-même**
* donc **les assets ne sont pas accessibles**
* même parfois bloqués sur le PC selon config

---

## ✅ Correction Vite (OBLIGATOIRE)

### Étape 1 — modifier `vite.config.js`

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: '192.168.1.7',
        },
    },
})
```

---

### Étape 2 — relancer Vite

```bash
npm run dev
```

Tu dois voir :

```
Local:   http://localhost:5173
Network: http://192.168.1.7:5173
```

---

## ✅ Résultat attendu

* ✅ PC : CSS chargé
* ✅ Téléphone : CSS chargé
* ✅ Accès via Wi-Fi

---

## ✅ Alternative (plus simple)

Pour éviter tout problème réseau :

```bash
npm run build
```

Et dans `.env` :

```env
APP_ENV=local
```

Laravel servira les assets compilés → aucun serveur Vite exposé.

---

## ✅ Récapitulatif

| Problème     | Cause                | Solution                |
| ------------ | -------------------- | ----------------------- |
| HTTPS bloqué | pas de SSL           | utiliser `http://`      |
| CSS absent   | Vite sur localhost   | exposer Vite sur réseau |
| Téléphone    | assets inaccessibles | config `host: 0.0.0.0`  |

---

## ✅ URL finales

* Laravel :

```
http://192.168.1.7:8001
```

* Vite (dev) :

```
http://192.168.1.7:5173
```

Le problème est purement réseau + HTTPS. Le backend fonctionne.
