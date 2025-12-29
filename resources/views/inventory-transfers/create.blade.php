<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Inventory Transfer') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="transferForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('inventory-transfers.store') }}" method="POST">
                        @csrf

                        <!-- Transfer Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Transfer Type</label>
                            <select name="type" x-model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="request_from_central">Request Stock from Central (Restock)</option>
                                <option value="send_to_doctor">Send Stock to Doctor</option>
                            </select>
                        </div>

                        <!-- Target Doctor (Only if sending) -->
                        <div class="mb-4" x-show="type === 'send_to_doctor'" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Target Doctor</label>
                            <select name="target_doctor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>

                        <!-- Items Section -->
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Items</h3>
                            
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-4 mb-2 items-end border p-4 rounded bg-gray-50">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Item Name</label>
                                        
                                        <!-- If Requesting from Central, Free Text or Search -->
                                        <!-- If Sending, select from My Inventory -->
                                        
                                        <template x-if="type === 'send_to_doctor'">
                                            <select :name="'items['+index+'][sku]'" x-model="item.sku" @change="updateItemName(index, $event)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="">Select Item</option>
                                                @foreach($myInventory as $inv)
                                                    <option value="{{ $inv->sku }}" data-name="{{ $inv->item_name }}">{{ $inv->item_name }} (Stock: {{ $inv->stock_qty }})</option>
                                                @endforeach
                                            </select>
                                        </template>
                                        
                                        <template x-if="type === 'request_from_central'">
                                            <input type="text" :name="'items['+index+'][item_name]'" x-model="item.item_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Item Name">
                                        </template>

                                        <!-- Hidden Input for Item Name when using Select -->
                                        <input type="hidden" :name="'items['+index+'][item_name]'" x-model="item.item_name">
                                    </div>
                                    
                                    <div class="w-32">
                                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" min="1">
                                    </div>
                                    
                                    <button type="button" @click="removeItem(index)" class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Remove</button>
                                </div>
                            </template>

                            <button type="button" @click="addItem()" class="mt-2 px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                                + Add Item
                            </button>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Create Transfer Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function transferForm() {
            return {
                type: 'request_from_central',
                items: [
                    { sku: '', item_name: '', quantity: 1 }
                ],
                addItem() {
                    this.items.push({ sku: '', item_name: '', quantity: 1 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                updateItemName(index, event) {
                    const selectedOption = event.target.selectedOptions[0];
                    if (selectedOption) {
                        this.items[index].item_name = selectedOption.getAttribute('data-name');
                    }
                }
            }
        }
    </script>
</x-app-layout>
