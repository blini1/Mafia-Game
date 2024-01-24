<!-- Modal Overlay -->
<div class="modal hidden fixed z-50 inset-0 overflow-y-auto" id="gameVotingModal" aria-labelledby="gameOutcomeModalLabel" aria-hidden="true">
    <!-- Modal Wrapper -->
    <div class="flex items-center justify-center min-h-screen">

        <!-- Modal Dialog -->
        <div class="modal-dialog relative bg-white rounded-lg shadow-xl">

            <!-- Modal Header -->
            <div class="modal-header flex justify-between items-center bg-gray-100 px-4 py-2 rounded-t-lg">
                <h5 class="modal-title font-semibold text-lg" id="gameVotingModalLabel">Vote to eliminate the player!</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <label for="playerVoteDropdown" class="block mb-2 text-sm font-medium text-gray-900">Select a player:</label>
                <select id="playerVoteDropdown" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <!-- Options will be dynamically added here -->
                </select>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer flex justify-end p-4 bg-gray-100 rounded-b-lg">
                <button type="button" class="btn-secondary mr-2 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600" onclick="closeModal()">Close</button>
                <button type="button" class="btn-primary px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" onclick="submitVote()">Cast Vote</button>
            </div>

        </div>
    </div>
</div>
