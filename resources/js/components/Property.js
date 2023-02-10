import React from 'react';

export default function Property({ property }) {
    return (
        <div>
            <div className="modal fade" id="getModal" tabIndex="-1" aria-labelledby="getModalLabel" aria-hidden="true">
            <div className="modal-dialog">
                <div className="modal-content">
                <div className="modal-header">
                    <h1 className="modal-title fs-5" id="getModalLabel">View property</h1>
                    <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div className="modal-body">
                    <div className="mb-3">
                    
                        <ul className="list-group list-group-flush">
                            {property.length > 0 && property.map((item) =>
                                <div key={item.id}>
                                    <li className="list-group-item">
                                        {item.name} - {item.relation}
                                    </li>
                                </div>
                            )}
                        </ul>
                    
                    </div>
                </div>
                <div className="modal-footer">
                </div>
                </div>
            </div>
            </div>
        </div>
    );
}