const Estimations = ({ estimations }) => {
    return (
        <div className="w-full">
            <div className="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div className="py-2 align-middle sm:px-6 lg:px-8">
                    <div className="overflow-hidden shadow sm:rounded-lg">
                        <table className="min-w-full divide-y divide-gray-100 shadow">
                            <thead className="bg-slate-800">
                            <tr>
                                <th scope="col" className="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Championship Prodictions</th>
                                <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-white">%</th>
                            </tr>
                            </thead>
                                <tbody className="divide-y divide-gray-100 bg-white">
                            {estimations.map((estimation, index) => <tr key={index}>
                                <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{estimation.name}</td>
                                <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-bold">{estimation.percent.toFixed(2)}</td>
                            </tr>)}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Estimations;
