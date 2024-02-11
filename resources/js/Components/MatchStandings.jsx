const MatchStandings = ({ matchStandings }) => {
    return (
        <div className="w-full max-w-[500px]">
            <div className="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div className="py-2 align-middle sm:px-6 lg:px-8">
                    <div className="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table className="min-w-full divide-y divide-gray-300 shadow">
                            <thead className="bg-slate-800">
                            <tr>
                                <th scope="col" className="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Team</th>
                                <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-white">W</th>
                                <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-white">D</th>
                                <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-white">L</th>
                                <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-white">GF</th>
                                <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-white">GA</th>
                                <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-white">PTS</th>
                            </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 bg-white">
                            {matchStandings.map((matchStading, index) => <tr key={index}>
                                <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{matchStading.team_name}</td>
                                <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{matchStading.win}</td>
                                <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{matchStading.draw}</td>
                                <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{matchStading.loss}</td>
                                <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{matchStading.goals_for}</td>
                                <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{matchStading.goals_against}</td>
                                <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{matchStading.points}</td>
                            </tr>)}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>
    )
}

export default MatchStandings;
