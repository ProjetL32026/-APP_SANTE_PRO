/*============================
         JS ACCEUIL 
============================*/




// ==================== DONNÉES ====================
    const medecinsData = {
      cardiologie: [
        { nom: "Dr. Ahmed Benali",  specialite: "Cardiologue interventionnel", exp: "12 ans" },
        { nom: "Dr. Sara Merabet",  specialite: "Cardiologue rythmologue",     exp: "9 ans"  },
        { nom: "Dr. Karim Hadjadj", specialite: "Cardiologue",                 exp: "15 ans" }
      ],
      ophtalmologie: [
        { nom: "Dr. Leila Bouzid",   specialite: "Ophtalmologue chirurgical", exp: "11 ans" },
        { nom: "Dr. Youssef Rahmani",specialite: "Spécialiste rétine",        exp: "14 ans" }
      ],
      dermatologie: [
        { nom: "Dr. Fatima Zahra Belkacem", specialite: "Dermatologue esthétique", exp: "10 ans" },
        { nom: "Dr. Sofiane Khelifi",       specialite: "Dermatologue",             exp: "13 ans" },
        { nom: "Dr. Amina Larbi",           specialite: "Spécialiste laser",        exp: "9 ans"  }
      ]
    };
 
    // ==================== FONCTIONS ====================
    function showDoctors(specialty) {
      document.getElementById('specialites-grid').classList.add('d-none');
      document.getElementById('btn-retour').classList.remove('d-none');
      document.getElementById('section-sub').textContent = '';
      document.getElementById('section-title').textContent =
        `Médecins en ${specialty.charAt(0).toUpperCase() + specialty.slice(1)}`;
 
      const container = document.getElementById('doctors-container');
      container.classList.remove('d-none');
      container.innerHTML = '';
 
      const doctors = medecinsData[specialty] || [];
      doctors.forEach(med => {
        container.innerHTML += `
          <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0 rounded-4 doctor-card">
              <div class="card-body text-center p-4 d-flex flex-column">
                <div class="doctor-avatar">👨‍⚕️</div>
                <h5 class="fw-bold text-aqua-dark mb-1">${med.nom}</h5>
                <p class="text-muted small mb-0">${med.specialite}</p>
                <p class="text-muted small mb-3">${med.exp} d'expérience</p>
                <button class="btn btn-rdv mt-auto w-100"
                  onclick="window.location.href='rdv.html'">Prendre rendez-vous
                </button>
              </div>
            </div>
          </div>`;
      });
    }
 
    function backToSpecialites() {
      document.getElementById('specialites-grid').classList.remove('d-none');
      document.getElementById('btn-retour').classList.add('d-none');
      document.getElementById('doctors-container').classList.add('d-none');
      document.getElementById('section-title').textContent = "Nos Spécialités";
      document.getElementById('section-sub').textContent =
        "Choisissez votre spécialité pour voir les médecins disponibles";
    }






