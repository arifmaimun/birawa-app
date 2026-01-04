<x-manual.layouts.app>
    <x-slot name="header">
        Create Invoice
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="invoiceForm()">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('manual.invoices.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Patient -->
                        <div>
                            <x-manual.input-label for="patient_id" value="Patient" />
                            <select id="patient_id" name="patient_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" {{ (old('patient_id') == $p->id || (isset($visit) && $visit->patient_id == $p->id)) ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->client->name ?? 'No Owner' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-manual.input-error :messages="$errors->get('patient_id')" class="mt-2" />
                            
                            @if(isset($visit) && $visit)
                                <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                                <div class="mt-2 text-xs text-blue-600">
                                    Creating invoice for Visit on {{ $visit->scheduled_at->format('d M Y') }}
                                </div>
                            @endif
                        </div>

                        <!-- Due Date -->
                        <div>
                            <x-manual.input-label for="due_date" value="Due Date" />
                            <x-manual.text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', date('Y-m-d'))" required />
                            <x-manual.input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Items</h3>
                            <button type="button" @click="addItem()" class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 text-sm font-semibold">
                                + Add Item
                            </button>
                        </div>
                        
                        <div class="border rounded-md overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Qty</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Price</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Total</th>
                                        <th class="px-4 py-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-4 py-2">
                                                <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="updatePrice(index)" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm text-right" required>
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" :name="'items['+index+'][price]'" x-model="item.price" min="0" step="0.01" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm text-right" required>
                                            </td>
                                            <td class="px-4 py-2 text-right font-medium text-gray-700">
                                                <span x-text="formatMoney(item.quantity * item.price)"></span>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right font-bold text-gray-700">Grand Total:</td>
                                        <td class="px-4 py-2 text-right font-bold text-indigo-700">
                                            <span x-text="formatMoney(calculateTotal())"></span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <x-manual.input-error :messages="$errors->get('items')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('manual.invoices.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <x-manual.primary-button class="ml-4">
                            {{ __('Create Invoice') }}
                        </x-manual.primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function invoiceForm() {
            return {
                items: [
                    { product_id: '', quantity: 1, price: 0 }
                ],
                products: {{ \Illuminate\Support\Js::from($products) }},
                
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, price: 0 });
                },
                
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                
                updatePrice(index) {
                    const productId = this.items[index].product_id;
                    const product = this.products.find(p => p.id == productId);
                    if (product) {
                        this.items[index].price = product.price || 0;
                    }
                },
                
                calculateTotal() {
                    return this.items.reduce((total, item) => {
                        return total + (item.quantity * item.price);
                    }, 0);
                },
                
                formatMoney(amount) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
                }
            }
        }
    </script>
</x-manual.layouts.app>
