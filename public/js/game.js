let gameId;
let userPlayerId;

document.getElementById('startGameButton').addEventListener('click', function() {
    fetch('/game/start', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            gameId = data.gameId;
            userRoleId = data.userRoleId;
            userRoleName = data.userRoleName;
            userPlayerId = data.userPlayerId;

            if (data.userEliminated) {
                handleUserElimination();
            } else {
                displayNightPhaseOutcome(data.nightPhaseResult);
            }
        })
        .catch(error => console.error('Error:', error));
});

document.getElementById('proceedWithDayPhaseButton').addEventListener('click', function() {
    startDayPhase();
});

document.getElementById('proceedWithNightPhaseButton').addEventListener('click', function() {
    startNightPhase();
});

function startDayPhase() {
    fetchAlivePlayers()
        .then(players => {
            if (players.length > 0) {
                showVotingModal(players);
                document.getElementById('proceedWithDayPhaseButton').classList.add('hidden');
            } else {
                console.error('No alive players found');
            }
        })
        .catch(error => console.error('Error fetching alive players:', error));
}

function startNightPhase() {
    // Call an API endpoint to simulate the night phase
    fetch(`/game/night-phase?game_id=${gameId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            displayNightPhaseOutcome(data.nightPhaseResult);
            document.getElementById('proceedWithNightPhaseButton').classList.add('hidden');
            // Show the day phase button if the game is still ongoing
            if (data.gameOver) {
                displayGameEnd(data.winner); // Function to handle game ending
            } else {
                // Regular handling of the night phase result
                document.getElementById('proceedWithDayPhaseButton').classList.remove('hidden');
                displayNightPhaseOutcome(data.nightPhaseResult);
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayNightPhaseOutcome(outcome) {
    const container = document.getElementById('gamePhases');
    if (!container) {
        console.error('Game phases container not found');
        return;
    }

    // Clear any previous content
    container.innerHTML = '';

    // Create a new element to display the outcome
    const outcomeElement = document.createElement('div');
    outcomeElement.classList.add('night-phase-outcome'); // Add Tailwind classes as needed

    // Display messages based on the outcome
    if (outcome.eliminatedPlayer) {
        outcomeElement.innerHTML = `During the night, Player ${outcome.eliminatedPlayer} was targeted.`;
        outcomeElement.innerHTML += ` Sadly, they were not saved and have been eliminated.`;
        // Check if the eliminated player is the current user
        if (outcome.eliminatedPlayer === userPlayerId) {
            handleUserElimination();
        }else{
            document.getElementById('startGameButton').classList.add('hidden');
            document.getElementById('proceedWithDayPhaseButton').classList.remove('hidden');
        }
    } else {
        outcomeElement.innerHTML = ` Fortunately, they were saved by the doctor.`;
    }
    container.appendChild(outcomeElement);
}

function handleUserElimination() {
    const gameOverMessage = document.createElement('div');
    gameOverMessage.classList.add('game-over-message'); // Add Tailwind classes as needed
    gameOverMessage.innerHTML = 'You have been eliminated from the game.';

    // Display the game over message
    const container = document.getElementById('gamePhases');
    container.appendChild(gameOverMessage);

    document.getElementById('startGameButton').classList.remove('hidden');
    document.getElementById('proceedWithDayPhaseButton').classList.add('hidden');
    document.getElementById('proceedWithNightPhaseButton').classList.add('hidden');
}

function sendVote(suspectId) {
    fetch('/game/vote', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            game_id: gameId,
            suspect_id: suspectId
        })
    })
        .then(response => response.json())
        .then(data => {
            // Update the UI with the voting outcome
            updatePhaseOutcome(data.dayPhaseResult);
            proceedToNextPhase();
        })
        .catch(error => console.error('Error:', error));
}

function proceedToNextPhase() {
    // Check game state to decide if the game continues or ends
    // For now, let's assume we proceed to the night phase
    document.getElementById('proceedWithNightPhaseButton').classList.remove('hidden');
}

function updatePhaseOutcome(dayPhaseResult) {
    if (dayPhaseResult.gameOver) {
        displayGameEnd(dayPhaseResult.winner);
    }

    // Find or create a container in your HTML to display the phase outcome
    const phaseOutcomeContainer = document.getElementById('gamePhases');
    if (!phaseOutcomeContainer) {
        console.error('Phase outcome container not found');
        return;
    }

    // Clear any previous content
    phaseOutcomeContainer.innerHTML = '';

    // Create a new element to display the outcome
    const outcomeElement = document.createElement('div');
    outcomeElement.classList.add('phase-outcome'); // Add any necessary classes

    // Display different messages based on the result
    if (dayPhaseResult.eliminatedPlayer) {
        outcomeElement.innerHTML = `Player ${dayPhaseResult.eliminatedPlayer} was eliminated.`;
    } else {
        outcomeElement.innerHTML = 'No player was eliminated in this phase.';
    }

    // Append the outcome element to the container
    phaseOutcomeContainer.appendChild(outcomeElement);

    // Optionally, add a button or link to proceed to the next phase or show final results
    // ...
}
function closeModal() {
    document.getElementById('gameVotingModal').classList.add('hidden');
}

function fetchAlivePlayers() {
    return fetch(`/game/alive-players?game_id=${gameId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }})
        .then(response => response.json())
        .then(data => data.players);
}

function showVotingModal(players) {
    const dropdown = document.getElementById('playerVoteDropdown');
    dropdown.innerHTML = '';

    players.forEach(player => {
        const option = document.createElement('option');
        option.value = player.id;
        option.textContent = player.name;
        dropdown.appendChild(option);
    });

    document.getElementById('gameVotingModal').classList.remove('hidden');
}

function submitVote() {
    const selectedPlayerId = document.getElementById('playerVoteDropdown').value;
    sendVote(selectedPlayerId);
    closeModal(); // Close the modal after voting
}

// Fetch alive players when proceeding to the next phase
document.getElementById('proceedWithDayPhaseButton').addEventListener('click', function() {
    fetchAlivePlayers()
        .then(players => {
            if (players.length > 0) {
                showVotingModal(players);
            } else {
                console.error('No alive players found');
                // Handle the scenario where no players are available for voting
            }
        })
        .catch(error => console.error('Error fetching alive players:', error));
});

function displayGameEnd(winner) {
    const message = winner === 'Villagers' ? 'Villagers win!' : 'Mafia wins!';
    alert(message); // Or update the UI more elegantly
    document.getElementById('startGameButton').classList.remove('hidden');
    // Hide other game phase buttons
    document.getElementById('proceedWithDayPhaseButton').classList.add('hidden');
    document.getElementById('proceedWithNightPhaseButton').classList.add('hidden');
    // Reset other game elements if necessary
}

