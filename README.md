# Note d’Intention : Canard Musclé

## Liens Importants

- **Backend / API (Symfony 7 + API Platform)**  
  [https://github.com/Canard-Systems/canard-muscle-api](https://github.com/Canard-Systems/canard-muscle-api)

- **Frontend / Nuxt (Vue 3, TailwindCSS…)**  
  [https://github.com/Canard-Systems/canard-muscle](https://github.com/Canard-Systems/canard-muscle)

- **Portfolio Master (Vue.js 3)**  
  [https://github.com/CallMeTrinity/antoninpamart.fr-master](https://github.com/CallMeTrinity/antoninpamart.fr-master)

- **Application Déployée (Front)**  
  [https://canard-muscle.antoninpamart.fr](https://canard-muscle.antoninpamart.fr)

- **API Déployée**  
  [https://canardmuscleapi.antoninpamart.fr](https://canardmuscleapi.antoninpamart.fr)

- **One‑page / Portfolio Master**  
  [https://master.antoninpamart.fr](https://master.antoninpamart.fr)

---

## 1. Contexte et Enjeux

Mon projet consiste à créer un **dashboard complet** pour gérer des entraînements sportifs, couvrant les exercices de musculation, la course, et potentiellement d’autres disciplines.  
L’idée de base est née d’un besoin personnel : j’utilise déjà des applications ou sites pour la course, la musculation, le suivi du poids, etc., **mais il manque une solution unique** où tout est centralisé.  
C’est ce qui a motivé la création de **Canard Musclé** – un service en ligne qui permet de rassembler **tous** les aspects de l’entraînement dans un même espace.

J’ai choisi ce domaine car j’aime le sport et je vois un véritable intérêt à un outil complet : la gestion d’exercices (définition, muscles ciblés, statut public/privé), la planification de séances (durée, distance, paramètres cardio ou muscu) et l’organisation de **plans d’entraînement** sur la durée, le tout associé à un calendrier pour visualiser et programmer ses séances. À terme, j’aimerais rendre Canard Musclé polyvalent, capable de s’adapter à différents types de sports et d’objectifs.

---

## 2. Objectifs et Fonctionnalités

Dès le départ, j’ai visé un **prototype fonctionnel** plutôt qu’une version finale aboutie, étant donné le temps restreint. Les objectifs clés étaient :

1. **Créer une API** solide, sur une base Symfony 7 et API Platform, afin de définir clairement le modèle de données (exercises, sessions, plans), gérer la persistance (Doctrine) et l’authentification (JWT tokens).
2. **Développer un front** Nuxt (Vue 3) pour proposer une interface interactive, avec Tailwind pour le style de base, Vuetify et v‑calendar pour la mise en forme de listes et le calendrier.
3. **Afficher** toutes les entités (exercises, séances, plans) dans un dashboard central, permettre la création/édition/suppression et donner un minimum de personnalisation à l’utilisateur (profil, login…).

Le résultat permet déjà :

- Gérer des exercices : nom, description, muscles, statut.
- Créer et modifier des séances : cardio ou non, durée, distance, association d’exercices, plan éventuel.
- Organiser les plans d’entraînement et associer des sessions.
- **Programmer** des séances sur un calendrier (vision jour, filtres par plan, etc.).
- Se connecter via JWT et stocker le token (crypté en BDD) pour 3 semaines.

---

## 3. Choix Techniques

- **Symfony 7 & API Platform** : j’ai souhaité maîtriser ce framework moderne pour bénéficier de la génération de documentation, du CRUD rapide et de la structure solide d’API Platform.
- **Doctrine ORM** : gère la BDD MySQL, hébergée chez Infomaniak, avec des relations typiques (un plan a plusieurs séances, une séance associe plusieurs exercices, etc.).
- **Nuxt / Vue 3** : un framework front-end progressif, très adapté aux SPA et au SSR. J’ai utilisé Vue 3 pour un code modulaire et dynamique, TailwindCSS pour le style rapide, Vuetify pour les composants (v-card, v-calendar…).
- **JWT tokens** : la sécurité par authentification stateless, pour de futures évolutions mobiles.
- **Hébergement** : manuel chez Infomaniak, en deux adresses distinctes (une pour l’API, l’autre pour le front).

---

## 4. Design et Expérience Utilisateur

Pour la démo, j’ai opté pour un style **futuriste/néon**, histoire d’avoir un rendu visuel cohérent, même s’il reste assez basique. À terme, je compte développer l’identité plus humoristique autour du concept **« Canard Musclé »**.  
J’ai fait en sorte que l’interface soit **responsive**, avec Tailwind et Vuetify, afin que l’application soit utilisable sur divers écrans (smartphone, tablette, desktop).

---

## 5. Difficultés et Méthodologie

La principale difficulté réside dans l’**ambition** du projet : je souhaitais créer un outil global, combinant diverses fonctionnalités avancées (notifications, algorithmes adaptatifs, suivi graphique détaillé).  
Le temps imparti étant limité, j’ai dû me recentrer sur un **prototype** fonctionnel. Les aspects d’admin avancé ou d’algorithmes évolués sont encore en chantier (restes de code commenté dans l’admin).

J’ai géré deux dépôts GitHub (front/back), et déployé à la main.  
J’ai perdu un peu de temps sur l’inscription en master et sur la **gestion du CORS** en production, mais j’ai pu franchir ces étapes en ajustant la configuration (NelmioCorsBundle, etc.).

---

## 6. Évolutions Futures

J’aimerais intégrer :

- **Génération adaptative des plans** : proposer des entraînements selon l’historique de l’utilisateur, ses préférences, ses performances.
- **Historique détaillé** et évolutions des séances : visualisation des progrès, courbes de performances, charges soulevées ou distances parcourues.
- **Notifications** motivantes de type Duolingo, rappelant à l’utilisateur de faire sa séance.
- **Module de partage** (générer un PDF, un lien public) et gestion d’un profil ultra détaillé (IMC, stats perso, etc.).
- **Application mobile** : s’appuyer sur l’API pour proposer un accès rapide depuis un smartphone, y compris hors ligne.

Je compte poursuivre ce projet, car je le trouve utile et modulable : on pourrait l’ouvrir aux sports collectifs, y intégrer d’autres modules (ex. nutrition, hydratation, etc.).

---

## 7. Conclusion

Finalement, **Canard Musclé** est un **projet personnel** ambitieux, dont j’ai présenté ici un prototype multi-entités (exercises, sessions, plans) permettant une vision centrale de la gestion sportive.  
Je suis content d’avoir pu découvrir et mettre en pratique **Symfony 7**, **API Platform**, **Nuxt 3**, **JWT tokens**, et la mise en place d’un **calendrier interactif**. Certes, plusieurs fonctionnalités sont encore à développer, mais l’essentiel est en place pour montrer la faisabilité et poser les bases d’une **future application complète** alliant performance, UX, et aspect ludique autour du concept de « Canard Musclé ».

Enfin, en bonus, j’ai rapidement monté un **mini-site one-page** ([master.antoninpamart.fr](https://master.antoninpamart.fr)) pour exposer un portfolio à jour, également en Vue.js 3. Cela montre ma volonté de toujours tester et apprendre, même dans un cadre de projet scolaire ou professionnel.
