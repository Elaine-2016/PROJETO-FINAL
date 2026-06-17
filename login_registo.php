<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="imagens/myfavicon.ico" type="image/x-icon">
    <title>VivaTickets | Entrar ou Registar</title>
    <meta name="description" content="VivaTickets: A sua plataforma de eleição para comprar bilhetes online. Descubra os melhores concertos, festivais e eventos imperdíveis. Rápido, seguro e intuitivo. Garanta o seu lugar agora!">
    <meta name="keywords" content="comprar bilhetes, venda de ingressos, bilheteira online, VivaTickets, concertos, festivais, eventos, agenda de eventos, reservar lugares, entretenimento, bilhetes online">  
    <meta property="og:type" content="website">
    <meta property="og:title" content="VivaTickets | <?= htmlspecialchars($evento['title'] ?? 'Bilheteira Online'); ?>">
    <meta property="og:description" content="Garanta os seus bilhetes para os melhores eventos.">
    <meta property="og:image" content="https://teusite.com/uploads/<?= htmlspecialchars($evento['image'] ?? 'default_event.jpg'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        
        <div class="text-center mb-5 mt-4">
            <h2 class="display-5 fw-bold">Aceder ao Viva<span class="text-lime">Tickets</span></h2>
            <p style="color: #a0a0a0;">Entra na tua conta ou junta-te a nós.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="card shadow-lg border-0" data-bs-theme="dark" style="background-color: #161616; border-radius: 24px;">
                    <div class="card-body p-5">
                        
                        <ul class="nav nav-tabs custom-tabs mb-4 justify-content-center" id="authTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#login">Login</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#registo">Registo</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="login">
                                <form action="processar_login.php" method="POST" class="needs-validation" novalidate>
                                    <div class="mb-3">
                                        <label class="form-label text-white">Email</label>
                                        <input type="email" name="email" class="form-control" 
                                            pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" 
                                            required placeholder="O teu email">
                                        
                                        <div class="invalid-feedback">
                                            Por favor, insere um email válido (exemplo: nome@dominio.pt).
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label text-white">Password</label>
                                        <input type="password" name="password" class="form-control" required placeholder="A tua password">
                                        <div class="invalid-feedback">
                                            A tua password tem de ter pelo menos 6 caracteres.
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-lime w-100">Entrar na Conta</button>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="registo">
                                <form action="processar_registo.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                    <div class="mb-3">
                                        <label class="form-label text-white">Nome Completo</label>
                                        <input type="text" name="name" class="form-control" required placeholder="Como te chamas?">
                                        <div class="invalid-feedback">
                                           Por favor, insere o teu nome completo.
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-white">Email</label>
                                        <input type="email" name="email" class="form-control" 
                                            pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" 
                                            required placeholder="O teu email">
                                        
                                        <div class="invalid-feedback">
                                            Por favor, insere um email válido (exemplo: nome@dominio.pt).
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-white">Password</label>
                                        <input type="password" name="password" class="form-control" minlength="6" required placeholder="Mínimo de 6 caracteres">
                                        <div class="invalid-feedback">
                                            A tua password tem de ter pelo menos 6 caracteres.
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-white">Confirmar Password</label>
                                        <input type="password" name="confirm_password" class="form-control" minlength="6" required placeholder="Repete a password">
                                        <div class="invalid-feedback">
                                            Confirma a tua password, tem de ter pelo menos 6 caracteres.
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label text-white">Foto de Perfil <span class="text-muted fs-6">(Opcional)</span></label>
                                        <input type="file" name="profile_pic" class="form-control" accept="image/*">
                                    </div>
                                    <button type="submit" class="btn btn-lime w-100">Criar Conta</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Ativa a validação visual do Bootstrap
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>